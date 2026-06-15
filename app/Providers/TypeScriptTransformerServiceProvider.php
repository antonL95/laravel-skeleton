<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeImmutable;
use Spatie\LaravelTypeScriptTransformer\TypeScriptTransformerApplicationServiceProvider as BaseTypeScriptTransformerServiceProvider;
use Spatie\TypeScriptTransformer\Transformers\AttributedClassTransformer;
use Spatie\TypeScriptTransformer\Transformers\EnumTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfigFactory;
use Spatie\TypeScriptTransformer\Writers\GlobalNamespaceWriter;

final class TypeScriptTransformerServiceProvider extends BaseTypeScriptTransformerServiceProvider
{
    #[\Override]
    protected function configure(TypeScriptTransformerConfigFactory $config): void
    {
        $config
            ->transformer(AttributedClassTransformer::class)
            ->transformer(EnumTransformer::class)
            ->transformDirectories(app_path())
            ->outputDirectory(resource_path('js/types'))
            ->writer(new GlobalNamespaceWriter('php-types.d.ts'))
            ->replaceType(DateTimeImmutable::class, 'string')
            ->replaceType(CarbonInterface::class, 'string')
            ->replaceType(CarbonImmutable::class, 'string')
            ->replaceType(Carbon::class, 'string');
    }
}
