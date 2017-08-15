<?php

namespace Polass\Tests;

use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

class MutatorTest extends TestCase
{
    /**
     * `mutate()` の引数にキーを指定しなかった場合のテスト
     *
     */
    public function testMutate()
    {
        $instance = new Stubs\UsedMutator;

        $answer = [
            'boolean'     => false,
            'integer'     => 1234,
            'numeric'     => 1.234,
            'date'        => Carbon::parse('2017-01-23'),
            'datetime'    => Carbon::parse('2017-01-23 12:34:56'),
            'date_format' => Carbon::parse('2017-01-23'),
            'foo'         => 'mutatedFOO',
            'bar'         => 'BAR',
        ];

        $this->assertEquals(
            $answer, $instance->mutate()->all()
        );

        $this->assertEquals(
            $answer, $instance->mutate()->all() // using cache
        );
    }

    /**
     * `mutate()` の引数にキーを指定した場合のテスト
     *
     */
    public function testMutateWithKey()
    {
        $instance = new Stubs\UsedMutator;

        $answer = [
            'boolean'     => false,
            'integer'     => 1234,
            'numeric'     => 1.234,
            'date'        => Carbon::parse('2017-01-23'),
            'datetime'    => Carbon::parse('2017-01-23 12:34:56'),
            'date_format' => Carbon::parse('2017-01-23'),
            'foo'         => 'mutatedFOO',
            'bar'         => 'BAR',
        ];

        foreach ($answer as $key => $value) {
            $this->assertEquals(
                $value, $instance->mutate($key)
            );
        }

        $this->assertNull(
            $instance->mutate('none')
        );

        $this->assertEquals(
            'FOO', $instance->mutate('baz', 'FOO')
        );
    }

    /**
     * `mutate()` のの引数にデフォルト値を持つ値を指定した場合のテスト
     *
     * @dataProvider provideInstanceWithDefaults
     */
    public function testMutateWithDefault($instance)
    {
        $this->assertEquals(
            'mutatedFOO', $instance->mutate('foo')
        );

        $this->assertEquals(
            'BAZ', $instance->mutate('baz')
        );

        $this->assertEquals(
            'FOO', $instance->mutate('baz', 'FOO')
        );
    }

    /**
     * `getDefaults()` のテスト
     *
     * @dataProvider provideInstanceWithDefaults
     */
    public function testGetDefaults($instance)
    {
        $this->assertTrue(
            is_array($instance->getDefaults()),
            'The `getDefaults()` method did not return an array.'
        );

        $this->assertEquals(
            [ 'foo' => 'bar', 'baz' => 'BAZ' ], $instance->getDefaults()
        );
    }

    /**
     * `hasDefault()` のテスト
     *
     * @dataProvider provideInstanceWithDefaults
     */
    public function testHasDefault($instance)
    {
        $this->assertTrue(
            $instance->hasDefault('foo')
        );

        $this->assertFalse(
            $instance->hasDefault('bar')
        );
    }

    /**
     * `getDefault()` のテスト
     *
     * @dataProvider provideInstanceWithDefaults
     */
    public function testGetDefault($instance)
    {
        $this->assertEquals(
            'bar', $instance->getDefault('foo')
        );

        $this->assertNull(
            $instance->getDefault('bar')
        );
    }

    /**
     * デフォルト値を持ったインスタンスを提供
     *
     * @return array
     */
    public function provideInstanceWithDefaults()
    {
        return [
            [ new Stubs\HasDefaultsField ],
            [ new Stubs\HasDefaultsMethod ],
            [ new Stubs\HasDefaultsFieldAndMethod ],
        ];
    }

    /**
     * `hasRule()` のテスト
     *
     */
    public function testHasRule()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertTrue(
            $instance->hasRule('foo')
        );

        $this->assertFalse(
            $instance->hasRule('none')
        );
    }

    /**
     * `getRule()` のテスト
     *
     */
    public function testGetRule()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertEquals(
            [ 'boolean' ], $instance->getRule('boolean')
        );

        $this->assertEquals(
            [ 'required', 'string' ], $instance->getRule('foo')
        );
    }

    /**
     * `hasBooleanRule()` のテスト
     *
     */
    public function testHasBooleanRule()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertTrue(
            $instance->hasBooleanRule('boolean')
        );

        $this->assertFalse(
            $instance->hasBooleanRule('integer')
        );
    }

    /**
     * `hasIntegerRule()` のテスト
     *
     */
    public function testHasIntegerRule()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertTrue(
            $instance->hasIntegerRule('integer')
        );

        $this->assertFalse(
            $instance->hasIntegerRule('boolean')
        );
    }

    /**
     * `hasNumericRule()` のテスト
     *
     */
    public function testHasNumericRule()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertTrue(
            $instance->hasNumericRule('numeric')
        );

        $this->assertFalse(
            $instance->hasNumericRule('boolean')
        );
    }

    /**
     * `hasDateRule()` のテスト
     *
     */
    public function testHasDateRule()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertTrue(
            $instance->hasDateRule('date')
        );

        $this->assertFalse(
            $instance->hasDateRule('date_format')
        );

        $this->assertFalse(
            $instance->hasDateRule('boolean')
        );
    }

    /**
     * `hasDateFormatRule()` のテスト
     *
     */
    public function testHasDateFormatRule()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertTrue(
            $instance->hasDateFormatRule('date_format')
        );

        $this->assertFalse(
            $instance->hasDateFormatRule('date')
        );

        $this->assertFalse(
            $instance->hasDateFormatRule('boolean')
        );
    }

    /**
     * `hasDateRules()` のテスト
     *
     */
    public function testHasDateRules()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertTrue(
            $instance->hasDateRules('date')
        );

        $this->assertTrue(
            $instance->hasDateRules('date_format')
        );

        $this->assertFalse(
            $instance->hasDateRules('boolean')
        );
    }

    /**
     * `getDateTimeFormat()` のテスト
     *
     */
    public function testGetDateTimeFormat()
    {
        $instance = new Stubs\UsedMutator;

        $this->assertEquals(
            'Y/m/d', $instance->getDateTimeFormat('date_format')
        );

        $this->assertNull(
            $instance->getDateTimeFormat('date')
        );

        $this->assertNull(
            $instance->getDateTimeFormat('boolean')
        );
    }
}
