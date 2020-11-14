<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    //登录页面
    public function create()
    {
        return view('sessions.create');
    }
    //非空验证密码
    public function store(Request $request)
    {
        $credentails = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentails,$request->has('remenber'))) {
            session()->flash('success', '欢迎回来');
            return redirect()->route('users.show', Auth::user());
        }else{
         session()->flash('danger','邮箱或密码不正确');
         return redirect()->back()->withInput();
        }
    }
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出');
        return redirect('login');
    }
}
