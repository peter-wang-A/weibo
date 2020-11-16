<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->activation_token = str::random(10);
        });
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //用户头像
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }


    // 一对多关系,一个用户有多条微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    //取出所有发布的微博并排序
    public function feed()
    {
        return $this->statuses()->orderBy('created_at', 'desc');
    }


    //获取粉丝列表
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    //获取关注的人列表
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    //关注
    public function follow($user_ids)
    {
        //如果不是数组转成数组，sync接收数组
        if (!is_array($user_ids)) {
            $user_ids = compact("user_ids");
        }
        //再关注人列表里添加关注人，存在则不添加
        $this->followings()->sync('user_ids', false);
    }

    //取消
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach('user_ids');
    }

    //判断当前登录的用户 A 是否关注了 B
    public function is_followings($user_ids)
    {
        $this->followings()->contains($user_ids);
    }
}
