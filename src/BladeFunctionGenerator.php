<?php

namespace MrEduar\S3M;

use MrEduar\S3M\Output\Script;

class BladeFunctionGenerator
{
    public function generate(): string
    {
        return (string) new Script($this->getHelperFunction());
    }

    private function getHelperFunction(): string
    {
        return file_get_contents(__DIR__.'/../dist/function.umd.js');
    }
}
