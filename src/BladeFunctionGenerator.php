<?php

namespace MrEduar\LaravelS3Multipart;

use MrEduar\LaravelS3Multipart\Output\Script;

class BladeFunctionGenerator
{
    public function generate(): string
    {
        return (string) new Script($this->getHelperFunction());
    }

    private function getHelperFunction(): string
    {
        return file_get_contents(__DIR__ . '/../dist/function.umd.js');
    }
}
