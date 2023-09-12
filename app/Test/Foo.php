<?php

namespace App\Test;

class Foo
{
    public int $foo;

    public function __construct(int $foo = 0)
    {
        $this->foo = $foo;
    }
}