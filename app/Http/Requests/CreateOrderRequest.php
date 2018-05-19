<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CreateOrderRequest extends Request
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
            'order_price' => 'required|numeric|min:0',
            'order_size' => 'required|numeric|min:0',
            'currency_id' => [
                'required',
                'numeric',
                Rule::exists('currencies', 'id')->where(function ($query) {
                    $query->where('short_name', '<>', 'USD');
                }),
            ],
            'order_side' => 'required|in:0,1',
            // 0 = sell, 1 = buy
            'order_type' => 'required|in:0,1,2'
            // 0 =LIMIT, 1=STOP, 2= MARKET
        ];
    }
}