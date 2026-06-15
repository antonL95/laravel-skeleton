<?php

declare(strict_types=1);

/*
 * Patch wayfinder's multi-route TypeScript generator.
 *
 * Upstream wayfinder (vendor/laravel/wayfinder) emits a keyed object when a
 * controller is bound to more than one route (e.g. a web route + an api/v1
 * route for the same invokable controller). This breaks every frontend call
 * site that uses the controller as a callable (Controller(uuid).url, ...).
 *
 * This patch rewrites multiRouteControllerMethod() to export a single callable
 * (the non-api route when available, otherwise the first route). Runs on every
 * composer post-autoload-dump so it survives composer install/update. Idempotent
 * (keyed on the SKELETON_WAYFINDER_PATCH marker).
 */

$target = __DIR__.'/../vendor/laravel/wayfinder/src/Langs/TypeScript/Converters/RouteMethod.php';

if (!is_file($target)) {
    exit(0);
}

$marker = 'SKELETON_WAYFINDER_PATCH_V1';
$contents = (string) file_get_contents($target);

if (str_contains($contents, $marker)) {
    exit(0);
}

$search = <<<'PHP'
    protected function multiRouteControllerMethod(): string
    {
        $output = [];

        foreach ($this->relatedRoutes as $route) {
            $routeMethod = new static(
                route: $route,
                withForm: $this->withForm,
                withInertiaComponent: $this->withInertiaComponent,
                named: $this->named,
                tmpMethod: true,
            );

            $output[] = $routeMethod->controllerMethod();
        }

        $object = TypeScript::object();

        foreach ($this->relatedRoutes as $route) {
            $object->key($route->uri())->value($this->tmpMethod($route));
        }

        $const = TypeScript::constant($this->name, $object)->export($this->named || ! $this->route->hasInvokableController());

        $output[] = (string) $const;

        return implode(PHP_EOL.PHP_EOL, $output);
    }
PHP;

$replace = <<<'PHP'
    // SKELETON_WAYFINDER_PATCH_V1: export a single callable when a controller
    // is bound to multiple routes (web + api/v1). Without this, frontend code
    // using Controller(arg).url fails with "not callable".
    protected function multiRouteControllerMethod(): string
    {
        $primary = null;

        foreach ($this->relatedRoutes as $candidate) {
            $uri = ltrim($candidate->uri(), '/');

            if (! str_starts_with($uri, 'api/')) {
                $primary = $candidate;

                break;
            }
        }

        if ($primary === null) {
            $primary = $this->relatedRoutes[0];
        }

        return (new static(
            route: $primary,
            withForm: $this->withForm,
            withInertiaComponent: $this->withInertiaComponent,
            named: $this->named,
            relatedRoutes: [$primary],
            tmpMethod: false,
        ))->controllerMethod();
    }
PHP;

if (!str_contains($contents, $search)) {
    fwrite(STDERR, "[patch-wayfinder] upstream multiRouteControllerMethod no longer matches expected shape; patch skipped.\n");
    exit(0);
}

$patched = str_replace($search, $replace, $contents);
file_put_contents($target, $patched);

fwrite(STDOUT, "[patch-wayfinder] applied.\n");
