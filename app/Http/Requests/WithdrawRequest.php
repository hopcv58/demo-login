<?php

namespace App\Http\Requests;

class WithdrawRequest extends Request
{
    use RequestTrait;

    /**
     * Get request contain only input in rule.
     * @var bool
     */
    protected $strict = true;

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                function($attribute, $value, $parameters, $validator) {
                    return $value == 0.06 ? false : true;
                }
            ],
            'amount' => 'required|numeric'
        ];
    }
}