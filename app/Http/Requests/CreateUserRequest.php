<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Http\Requests\RequestTrait;

class CreateUserRequest extends Request
{
    use RequestTrait;

    /**
     * Get request contain only input in rule.
     * @var bool
     */
    protected $strict = true;

    /**
     * Sanitize request.
     * @param Request $request
     */
    public function sanitize(Request $request)
    {
        // Group all inputs filter_ to array filters.
        $this->groupFilters();
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }
}