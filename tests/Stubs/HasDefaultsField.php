<?php

namespace Polass\Tests\Stubs;

class HasDefaultsField extends UsedMutator
{
    public $defaults = [
        'foo' => 'bar',
        'baz' => 'BAZ',
    ];
}
