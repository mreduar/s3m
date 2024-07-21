<?php

use MrEduar\LaravelS3Multipart\BladeFunctionGenerator;

test('render script tag', function () {
    $routeFunction = file_get_contents(__DIR__.'/../../../dist/function.umd.js');

    expect((new BladeFunctionGenerator)->generate())->toBe(
        <<<HTML
        <script type="text/javascript">{$routeFunction}</script>
        HTML
    );
});

test('compile blade directive', function (string $blade, string $output) {
    expect(app('blade.compiler')->compileString($blade))->toBe($output);
})->with([
    ['@s3m', "<?php echo app('MrEduar\LaravelS3Multipart\BladeFunctionGenerator')->generate(); ?>"],
]);
