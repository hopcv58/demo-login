<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Factories\ActivationFactory;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * @var ActivationFactory $activationFactory
     */
    protected $activationFactory;

    /**
     * Create a new controller instance.
     *
     * @param $activationFactory
     * @return void
     */
    public function __construct(ActivationFactory $activationFactory)
    {
        $this->middleware('guest');
        $this->activationFactory = $activationFactory;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|min:4|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone_number' => 'required|min:6|max:255',
            'country_code' => 'required|integer|min:100000|max:999999',
            'password' => 'required|confirmed|min:6|max:50',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'activated' => 1,
            'rating' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'country_code' => $data['country_code'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function activateUser($token)
    {
        if ($user = $this->activationFactory->activateUser($token)) {
            auth()->login($user);
            return redirect($this->redirectPath());
        }
        abort(404);
    }
}
