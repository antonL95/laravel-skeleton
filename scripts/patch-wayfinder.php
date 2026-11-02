<?php

declare(strict_types=1);

$target = __DIR__.'/../vendor/laravel/wayfinder/src/Langs/TypeScript/Converters/RouteMethod.php';

if (!is_file($target)) {
    exit(0);
}

$contents = (string) file_get_contents($target);
$routeMethodPatched = str_contains($contents, 'relatedRoutes: [$primary],');

$search = <<<'PHP'
    protected function multiRouteControllerMethod(): string
    {
        $output = [];

        $duplicateUris = collect($this->relatedRoutes)->duplicates(fn (Route $route) => $route->uri());

        $keys = array_map(
            fn (Route $route) => $duplicateUris->contains($route->uri())
                ? $this->verbPrefixedUri($route)
                : $route->uri(),
            $this->relatedRoutes,
        );

        foreach ($this->relatedRoutes as $index => $route) {
            $routeMethod = new static(
                route: $route,
                withForm: $this->withForm,
                withInertiaComponent: $this->withInertiaComponent,
                named: $this->named,
                tmpMethod: true,
                tmpMethodKey: $keys[$index],
            );

            $output[] = $routeMethod->controllerMethod();
        }

        $object = TypeScript::object();

        foreach ($this->relatedRoutes as $index => $route) {
            $object->key($keys[$index])->value($this->tmpMethod($route, $keys[$index]));
        }

        $const = TypeScript::constant($this->name, $object)->export($this->named || ! $this->route->hasInvokableController());

        $output[] = (string) $const;

        return implode(PHP_EOL.PHP_EOL, $output);
    }
PHP;

$replace = <<<'PHP'
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

if (!$routeMethodPatched && !str_contains($contents, $search)) {
    fwrite(STDERR, "[patch-wayfinder] upstream multiRouteControllerMethod no longer matches expected shape; patch skipped.\n");
} elseif (!$routeMethodPatched) {
    $patched = str_replace($search, $replace, $contents);
    file_put_contents($target, $patched);
    fwrite(STDOUT, "[patch-wayfinder] RouteMethod patch applied.\n");
}

$runtimeTarget = __DIR__.'/../vendor/laravel/wayfinder/resources/js/wayfinder.ts';
$runtimeContents = is_file($runtimeTarget) ? (string) file_get_contents($runtimeTarget) : '';
$runtimeSearch = 'type Method = "get" | "post" | "put" | "delete" | "patch" | "head" | "options";';
$runtimeReplace = 'type Method = "get" | "post" | "put" | "delete" | "patch" | "head" | "options" | "query";';

if ($runtimeContents !== '' && !str_contains($runtimeContents, '| "query"') && !str_contains($runtimeContents, $runtimeSearch)) {
    fwrite(STDERR, "[patch-wayfinder] upstream runtime Method type no longer matches expected shape; query patch skipped.\n");
} elseif ($runtimeContents !== '' && !str_contains($runtimeContents, '| "query"')) {
    file_put_contents($runtimeTarget, str_replace($runtimeSearch, $runtimeReplace, $runtimeContents));
    fwrite(STDOUT, "[patch-wayfinder] runtime Method query patch applied.\n");
}

$broadcastTarget = __DIR__.'/../vendor/laravel/wayfinder/src/Converters/BroadcastEvents.php';

if (!is_file($broadcastTarget)) {
    exit(0);
}

$broadcastMarker = 'TENSEN_WAYFINDER_PATCH_V1_ECHO';
$broadcastContents = (string) file_get_contents($broadcastTarget);

if (str_contains($broadcastContents, $broadcastMarker)) {
    exit(0);
}

$broadcastSearch = <<<'PHP'
        return implode(PHP_EOL, $content).PHP_EOL.TypeScript::module(
            $echoPackage,
            TypeScript::interface('Events', $eventsInterface->implode(PHP_EOL))
        );
PHP;

$broadcastReplace = <<<'PHP'
        // TENSEN_WAYFINDER_PATCH_V1_ECHO: append `export {};` so the .d.ts is a
        // module file and `declare module '@laravel/echo-react'` augments the
        // real type instead of replacing it.
        return implode(PHP_EOL, $content).PHP_EOL.TypeScript::module(
            $echoPackage,
            TypeScript::interface('Events', $eventsInterface->implode(PHP_EOL))
        ).PHP_EOL.PHP_EOL.'export {};';
PHP;

if (!str_contains($broadcastContents, $broadcastSearch)) {
    fwrite(STDERR, "[patch-wayfinder] upstream echoFileContent no longer matches expected shape; echo patch skipped.\n");
    exit(0);
}

$broadcastPatched = str_replace($broadcastSearch, $broadcastReplace, $broadcastContents);
file_put_contents($broadcastTarget, $broadcastPatched);

fwrite(STDOUT, "[patch-wayfinder] BroadcastEvents patch applied.\n");
