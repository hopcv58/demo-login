<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

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
                Rule::exists('balances', 'currency_id')->where(function ($query) {
                    $query->where('user_id', $this->user()->id);
                }),
            ],
            'amount' => 'required|numeric'
        ];
    }
}