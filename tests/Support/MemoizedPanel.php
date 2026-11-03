<?php

declare(strict_types=1);

namespace Tests\Support;

use Filament\Clusters\Cluster;
use Filament\Panel;
use Livewire\Component;

final class MemoizedPanel extends Panel
{
    /** @var array<string, array{register: array<string, class-string<Component>>, livewire: array<string, class-string>, clustered: array<class-string<Cluster>, array<class-string>>}> */
    private static array $discovered = [];

    public function discoverComponents(string $baseClass, array &$register, ?string $directory, ?string $namespace): void
    {
        $key = serialize([$this->getId(), $baseClass, $directory, $namespace]);

        if (!isset(self::$discovered[$key])) {
            $before = $register;
            $livewire = $this->livewireComponents;
            $clustered = $this->clusteredComponents;
            $register = [];
            $this->livewireComponents = [];
            $this->clusteredComponents = [];

            try {
                parent::discoverComponents($baseClass, $register, $directory, $namespace);

                self::$discovered[$key] = [
                    'register' => $register,
                    'livewire' => $this->livewireComponents,
                    'clustered' => $this->clusteredComponents,
                ];
            } finally {
                $register = $before;
                $this->livewireComponents = $livewire;
                $this->clusteredComponents = $clustered;
            }
        }

        $register = array_merge($register, self::$discovered[$key]['register']);
        $this->livewireComponents = array_merge($this->livewireComponents, self::$discovered[$key]['livewire']);
        foreach (self::$discovered[$key]['clustered'] as $cluster => $components) {
            $this->clusteredComponents[$cluster] = array_merge($this->clusteredComponents[$cluster] ?? [], $components);
        }
    }
}
