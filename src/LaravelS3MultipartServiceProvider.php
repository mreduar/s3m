<?php

namespace MrEduar\LaravelS3Multipart;

use Illuminate\View\Compilers\BladeCompiler;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelS3MultipartServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        if ($this->app->resolved('blade.compiler')) {
            $this->registerDirective($this->app['blade.compiler']);
        } else {
            $this->app->afterResolving('blade.compiler', fn (BladeCompiler $blade) => $this->registerDirective($blade));
        }

        $package
            ->name('laravel-s3-multipart')
            ->hasConfigFile();
    }

    protected function registerDirective(BladeCompiler $blade)
    {
        $blade->directive('s3m', fn () => "<?php echo app('".BladeFunctionGenerator::class."')->generate(); ?>");
    }
}
