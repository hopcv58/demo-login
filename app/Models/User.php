<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
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
     * @param $identifier
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function findForPassport($identifier) {
        return $this->where('email', $identifier)->first();
    }

    /**
     * @param array $demands
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function createNewUser(array $demands) {
        return DB::table('users')->insertGetId($demands);
    }

    /**
     * @param $id
     * @param array $demands
     * @return int
     */
    public function updateUserById($id, array $demands) {
        return User::query()->where('id', $id)->update($demands);
    }
}
