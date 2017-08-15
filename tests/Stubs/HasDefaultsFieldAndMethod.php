<?php

namespace Polass\Tests\Stubs;

class HasDefaultsFieldAndMethod extends UsedMutator
{
    public $defaults = [
        'foo' => 'dummy',
    ];

    public function defaults()
    {
        return [
            'foo' => 'bar',
            'baz' => 'BAZ',
        ];
    }
}
