<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function passwordSecurity()
    {
        return $this->hasOne(PasswordSecurity::class);
    }

    /**
     * Setup use username column as login name. Default: email
     * @param $identifier
     * @return mixed
     */
    public function findForPassport($identifier) {
        return $this->where('email', $identifier)->first();
    }

    public function getAllUsers(array $demands) {
        return $this->where('email', $demands['email']);
    }
}
