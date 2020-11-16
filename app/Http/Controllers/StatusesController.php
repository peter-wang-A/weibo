<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class StatusesController extends Controller
{
    //必须登录后才能执行下列方法
    public function __construct()
    {
        $this->middleware('auth');
    }

    //创建用户
    public function store(Request $request)
    {
        //非空验证
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        //保存数据
        Auth::user()->statuses()->create([
            'content' => $request['content']
        ]);
        session()->flash('success', '发布成功！');
        return redirect()->back();
    }
}
