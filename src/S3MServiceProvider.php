<?php

namespace MrEduar\S3M;

use Illuminate\View\Compilers\BladeCompiler;
use MrEduar\S3M\Http\Controllers\S3MultipartController;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class S3MServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $this->app->singleton(
            Contracts\StorageMultipartUploadControllerContract::class,
            S3MultipartController::class
        );

        if ($this->app->resolved('blade.compiler')) {
            $this->registerDirective($this->app['blade.compiler']);
        } else {
            $this->app->afterResolving('blade.compiler', fn (BladeCompiler $blade) => $this->registerDirective($blade));
        }

        $package
            ->name('s3m')
            ->hasRoute('web');
    }

    protected function registerDirective(BladeCompiler $blade)
    {
        $blade->directive('s3m', fn () => "<?php echo app('".BladeFunctionGenerator::class."')->generate(); ?>");
    }
}
