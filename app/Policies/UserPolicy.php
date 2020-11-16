<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    //权限验证，只有用户自己才能修在自己的资料
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
    //只允许已登录的管理员才能删除用户
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin &&  $currentUser->id !== $user->id;
    }

    //关注
    public function follow(User $currentUser, User $user)
    {
        return $currentUser->id !== $user->id;
    }
}
