<?php
/**
 * Created by ASUS.
 * Date: 7/21/2017
 * Time: 2:53 PM
 */

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

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
     * Get all users.
     * @param array $demands
     * @return Collection
     */
    public function getAllUsers(array $demands)
    {
        return $this->users->getAllUsers($demands);
    }
}