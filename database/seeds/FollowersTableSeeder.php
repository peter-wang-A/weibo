<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //取出所有用户
        $users = User::all();
        $user = $users->first();
        $user_id = $user->id;

        //获取除了 1 号用户以为的其他用户 ID 数组
        $followers = $users->slice(1);
        $followers_ids = $followers->pluck('id')->toArray();

        //关注除了 1 号用户以外的是所有用户
        $user->follow($followers_ids);

        //除了 1 号以外的所有用户都来关注 1 号
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
    }
}
