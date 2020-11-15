<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        //中间件过滤，except 方法过滤不需要登录页面，其他马面登录后菜课访问
        // $this->middleware('auth',[
        //     'except'=>['show','create','store']
        // ]);
        $this->middleware('auth', [
            'except' => ['create', 'store']
        ]);
        //只让未登录用户访问注册页面
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
        return view('users.show', compact('user'));
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
        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
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
    public function index(){
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }
}
