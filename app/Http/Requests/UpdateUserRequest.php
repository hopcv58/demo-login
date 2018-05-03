<?php

namespace App\Http\Requests;

class UpdateUserRequest extends Request
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
            'name' => 'required_without_all:email,phone_number,country_code,password|string|min:4|max:255',
            'email' => 'required_without_all:name,phone_number,country_code,password|email|max:255|unique:users,email,' . $this->user()->id,
            'phone_number' => 'required_without_all:email,name,country_code,password|string|min:6|max:255',
            'country_code' => 'required_without_all:email,phone_number,name,password|integer|min:100000|max:999999',
            'password' => 'required_without_all:email,phone_number,country_code,name|string|min:6|max:50',
        ];
    }
}