<?php

namespace App\Http\Requests;

class DepositRequest extends Request
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
            'currency_id' => 'required|integer|exists:currencies,id',
            'amount' => 'required|numeric'
        ];
    }
}