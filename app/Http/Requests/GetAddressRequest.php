<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class GetAddressRequest extends Request
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
                Rule::exists('currencies', 'id')->where(function ($query) {
                    $query->where('short_name', '<>', 'USD');
                }),
            ]
        ];
    }
}