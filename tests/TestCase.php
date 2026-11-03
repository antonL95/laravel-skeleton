<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Override;
use Tests\Support\TestingServiceProvider;

abstract class TestCase extends BaseTestCase
{
    #[Override]
    final public function createApplication(): Application
    {
        RegisterProviders::merge([TestingServiceProvider::class]);

        return parent::createApplication();
    }
}
