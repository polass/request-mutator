<?php

namespace Polass\Tests\Stubs;

use Polass\RequestMutator\Mutator;

class UsedMutator
{
    use Mutator;

    /**
     * バリデーションルールを取得
     *
     * @return array
     */
    public function rules()
    {
        return [
            'boolean'     => [ 'boolean' ],
            'integer'     => [ 'integer' ],
            'numeric'     => [ 'numeric' ],
            'date'        => [ 'date' ],
            'datetime'    => [ 'date' ],
            'date_format' => [ 'date_format:Y/m/d' ],
            'foo'         => 'required|string',
        ];
    }

    /**
     * パラメータを取得
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        $input = [
            'boolean'     => 'false',
            'integer'     => '1234',
            'numeric'     => '1.234',
            'date'        => '2017-01-23',
            'datetime'    => '2017-01-23 12:34:56',
            'date_format' => '2017/01/23',
            'foo'         => 'FOO',
            'bar'         => 'BAR',
        ];

        return isset($key) ? ($input[$key] ?? $default) : $input;
    }

    /**
     * パラメータ `foo` を変換
     *
     * @param mixed $value
     * @return mixed
     */
    public function mutateFooAttribute($value)
    {
        return "mutated{$value}";
    }
}
