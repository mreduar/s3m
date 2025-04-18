<?php

namespace MrEduar\S3M\Tests;

use Mockery;
use MrEduar\S3M\S3MServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            S3MServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:IIMuLr30O1WIrgP+3azRrOUgLGYcb5zqfZzeeChrPSg=');
    }
}
