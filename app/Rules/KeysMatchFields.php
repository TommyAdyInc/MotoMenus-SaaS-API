<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class KeysMatchFields implements Rule
{
    private $model;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $keys = array_keys(json_decode($value, true));
        $diff = array_diff($keys, $this->model->getFillable());

        return !count($diff);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The array keys do not match the database fields.';
    }
}
