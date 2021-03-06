<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Contracts\Pagination\Paginator;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        //中间件过滤，except 方法过滤不需要登录页面，其他页面登录后可访问
        // $this->middleware('auth',[
        //     'except'=>['show','create','store']
        // ]);
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail', 'signup']
        ]);
        //只有未登录用户才能访问的页面
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    //用户注册
    public function signup()
    {
        return view('users.create');
    }
    //显示用户页面
    public function show(User $user)
    {
        $statuses = $user->statuses()->orderBy('created_at', 'desc')->paginate(30);
        return view('users.show', compact('user', 'statuses'));
    }
    //用户注册
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        //将用户注册数据插入到数据库
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }
    //编辑资料页面
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    //更新数据
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        //验证输入信息
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
        $data = [];
        $data['name'] = $request->name;
        //如果又密码则更改没有则不更改
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        //插入数据库
        $user->update($data);
        session()->flash('success', '个人资料修改成功');
        return redirect()->route('users.show', $user->id);
    }
    //用户列表
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
    //删除用户
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户!');
        return back();
    }

    //发送邮件给指定用户
    // public function sendEmailConfirmationTo($user)
    // {
    //     $view = 'emails.confirm';
    //     $data = compact('user');
    //     $from = 'summer@example.com';
    //     $name = 'Summer';
    //     $to = $user->email;
    //     $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

    //     Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
    //         $message->from($from, $name)->to($to)->subject($subject);
    //     });
    // }
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'summer@example.com';
        $name = 'Summer';
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    //激活路由
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜，成功登录');
        return redirect()->route('users.show', [$user]);
    }

    //followings 显示用的关注人列表
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }
    //显示粉丝列表
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
