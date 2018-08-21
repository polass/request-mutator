<?php

namespace Polass\RequestMutator;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait Mutator
{
    /**
     * 変換したパラメータのコレクション
     *
     * @var \Illuminate\Support\Collection
     */
    protected $mutatedInputs = null;

    /**
     * パラメータを取得
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    abstract public function input($key = null, $default = null);

    /**
     * リクエストパラメータを変換する
     *
     * @param string|null $key
     * @param mixed $default
     * @return \Illuminate\Support\Collection
     */
    public function mutate($key = null, $default = null)
    {
        if (isset($key)) {
            return $this->mutateAttribute($key, $this->input($key, $default));
        }

        if (is_null($this->mutatedInputs)) {
            $this->mutatedInputs = collect();

            foreach ($this->input() as $key => $value) {
                $this->mutatedInputs->put($key, $this->mutateAttribute($key, $value));
            }
        }

        return $this->mutatedInputs;
    }

    /**
     * 指定したパラメータを変換
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function mutateAttribute($key, $value, $default = null)
    {
        if (is_null($value)) {
            if (isset($default)) {
                return $default;
            }

            if ($this->hasDefault($key)) {
                return $this->getDefault($key);
            }
        }

        if ($this->hasMutator($key)) {
            return $this->{$this->getMutator($key)}($value);
        }

        if (isset($value)) {
            if ($this->hasBooleanRule($key)) {
                return $this->asBoolean($value);
            }

            if ($this->hasIntegerRule($key)) {
                return $this->asInteger($value);
            }

            if ($this->hasNumericRule($key)) {
                return $this->asNumeric($value);
            }

            if ($this->hasDateRules($key) && $value) {
                return $this->asDateTime($value);
            }

            if ($this->hasDateFormatRule($key) && $value) {
                return $this->asDateTime($value, $this->getDateTimeFormat($key));
            }
        }

        return $value;
    }

    /**
     * デフォルト値を取得
     *
     * @return array
     */
    public function getDefaults()
    {
        if (method_exists($this, 'defaults')) {
            return $this->defaults();
        }

        if (property_exists($this, 'defaults')) {
            return $this->defaults;
        }

        return [];
    }

    /**
     * 指定したパラメータのデフォルト値が設定されているか
     *
     * @param string $key
     * @return bool
     */
    public function hasDefault($key)
    {
        return array_key_exists($key, $this->getDefaults());
    }

    /**
     * 指定したパラメータのデフォルト値を取得
     *
     * @param string $key
     * @return mixed
     */
    public function getDefault($key)
    {
        if ($this->hasDefault($key)) {
            return $this->getDefaults()[$key];
        }

        return null;
    }

    /**
     * 指定したパラメータに対応する変換処理のメソッド名を取得
     *
     * @param string $key
     * @return string
     */
    protected function getMutator($key)
    {
        return 'mutate'.Str::studly($key).'Attribute';
    }

    /**
     * 指定したパラメータに対応する変換処理を持っているか
     *
     * @param string $key
     * @return bool
     */
    protected function hasMutator($key)
    {
        return method_exists($this, $this->getMutator($key));
    }

    /**
     * 指定したパラメータに対応するバリデーションを持っているか
     *
     * @param string $key
     * @return bool
     */
    public function hasRule($key)
    {
        if (method_exists($this, 'rules')) {
            return array_key_exists($key, $this->rules());
        }
    }

    /**
     * 指定したパラメータに対応するバリデーションルールを取得
     *
     * @param string $key
     * @return array
     */
    public function getRule($key)
    {
        if ($this->hasRule($key)) {
            if (! is_array($rule = $this->rules()[$key])) {
                return array_map('trim', explode('|', $rule));
            }

            return $rule;
        }

        return [];
    }

    /**
     * バリデーションルール `boolean` が設定されているか
     *
     * @param string $key
     * @return bool
     */
    public function hasBooleanRule($key)
    {
        return in_array('boolean', $this->getRule($key));
    }

    /**
     * Bool 型に変換
     *
     * @param mixed $value
     * @return bool
     */
    protected function asBoolean($value)
    {
        return $value && 'false' !== $value;
    }

    /**
     * バリデーションルール `integer` が設定されているか
     *
     * @param string $key
     * @return bool
     */
    public function hasIntegerRule($key)
    {
        return in_array('integer', $this->getRule($key));
    }

    /**
     * 整数に変換
     *
     * @param mixed $value
     * @return int
     */
    protected function asInteger($value)
    {
        return (int) $value;
    }

    /**
     * バリデーションルール `numeric` が設定されているか
     *
     * @param string $key
     * @return bool
     */
    public function hasNumericRule($key)
    {
        return in_array('numeric', $this->getRule($key));
    }

    /**
     * 数値に変換
     *
     * @param mixed $value
     * @return float
     */
    protected function asNumeric($value)
    {
        return (float) $value;
    }

    /**
     * バリデーションルール `date` が設定されているか
     *
     * @param string $key
     * @return bool
     */
    public function hasDateRule($key)
    {
        return in_array('date', $this->getRule($key));
    }

    /**
     * バリデーションルール `date_format` が設定されているか
     *
     * @param string $key
     * @return bool
     */
    public function hasDateFormatRule($key)
    {
        foreach ($this->getRule($key) as $rule) {
            if (is_string($rule) && Str::startsWith($rule, 'date_format:')) {
                return true;
            }
        }

        return false;
    }

    /**
     * 日付型を表すバリデーションルールが設定されているか
     *
     * @param string $key
     * @return bool
     */
    public function hasDateRules($key)
    {
        return $this->hasDateRule($key) || $this->hasDateFormatRule($key);
    }

    /**
     * バリデーションルール `date_format` で指定されたフォーマットを取得
     *
     * @param string $key
     * @return string
     */
    public function getDateTimeFormat($key)
    {
        foreach ($this->getRule($key) as $rule) {
            if (preg_match('/\Adate_format:(.*)\z/i', $rule, $matches)) {
                return $matches[1];
            }
        }
    }

    /**
     * 日付型を表すバリデーションルールに従って値を Carbon 型に変換
     *
     * @param mixed $value
     * @param string $format
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value, $format = null)
    {
        if ($format !== null) {
            return Carbon::createFromFormat($format, $value);
        }

        return Carbon::parse($value);
    }
}
