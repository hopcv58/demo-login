<?php
/**
 * Created by ASUS.
 * Date: 7/21/2017
 * Time: 2:53 PM
 */

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;

class UsersRepository extends Repository
{
    /**
     * @var User $users
     */
    private $users;

    /**
     * UsersRepository constructor.
     */
    public function __construct()
    {
        $this->users = new User();
    }

    /**
     * @param array $demands
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function createNewUser(array $demands)
    {
        $demands['password'] = bcrypt($demands['password']);
        $demands['activated'] = 1;
        $demands['rating'] = 0;
        $demands['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
        return $this->users->createNewUser($demands);
    }

    /**
     * @param $id
     * @param array $demands
     * @return int
     */
    public function updateUserById($id, array $demands)
    {
        $demands['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        if (array_key_exists('password', $demands)) {
            $demands['password'] = bcrypt($demands['password']);
        }

        return $this->users->updateUserById($id, $demands);
    }
}