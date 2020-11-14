<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
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
        return view('users.edit', compact('user'));
    }
    //更新数据
    public function update(User $user, Request $request)
    {
        //验证输入信息
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);
        $data=[];
        $data['name']=$request->name;
        if($request->password){
            $data['password']=bcrypt($request->password);
        }
        //插入数据库
        $user->update($data);
        session()->flash('success','个人资料修改成功');
        return redirect()->route('users.show', $user->id);
    }
}
