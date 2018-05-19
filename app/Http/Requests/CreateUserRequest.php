<?php

namespace App\Http\Requests;

class CreateUserRequest extends Request
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
            'name' => 'required|min:4|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone_number' => 'required|min:6|max:255',
            'country_code' => 'required|integer|min:100000|max:999999',
            'password' => 'required|min:8|max:50|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}/u',
        ];
    }
}