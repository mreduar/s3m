<?php

namespace MrEduar\LaravelS3Multipart\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use MrEduar\LaravelS3Multipart\LaravelS3MultipartServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MrEduar\\LaravelS3Multipart\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelS3MultipartServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-s3-multipart_table.php.stub';
        $migration->up();
        */
    }
}
