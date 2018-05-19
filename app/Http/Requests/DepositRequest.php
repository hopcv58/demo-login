<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

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
            'amount' => 'required|numeric'
        ];
    }
}