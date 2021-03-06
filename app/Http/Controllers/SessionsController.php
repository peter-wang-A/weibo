<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    //登录页面
    public function create()
    {
        return view('sessions.create');
    }
    //登录
    public function store(Request $request)
    {
        //非空验证密码
        $credentails = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        //验证是否激活
        if (Auth::attempt($credentails, $request->has('remenber'))) {
            if (Auth::user()->activated) {
                session()->flash('success', '欢迎回来');
                $fallback = route('users.show', Auth::user());
                return redirect()->intended($fallback);
            } else {
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的邮件激活');
                return redirect('/');
            }
        } else {
            session()->flash('danger', '邮箱或密码不正确');
            return redirect()->back()->withInput();
        }
    }
    //退出
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出');
        return redirect('login');
    }
}
