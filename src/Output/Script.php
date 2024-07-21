<?php

namespace MrEduar\S3M\Output;

use Stringable;

class Script implements Stringable
{
    protected $function;

    public function __construct(string $function)
    {
        $this->function = $function;
    }

    public function __toString(): string
    {
        return <<<HTML
<script type="text/javascript">{$this->function}</script>
HTML;
    }
}
