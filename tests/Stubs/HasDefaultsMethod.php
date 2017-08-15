<?php

namespace Polass\Tests\Stubs;

class HasDefaultsMethod extends UsedMutator
{
    public function defaults()
    {
        return [
            'foo' => 'bar',
            'baz' => 'BAZ',
        ];
    }
}
