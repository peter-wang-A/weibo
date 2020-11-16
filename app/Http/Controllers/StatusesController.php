<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Status;

class StatusesController extends Controller
{
    //必须登录后才能执行下列方法
    public function __construct()
    {
        $this->middleware('auth');
    }

    //创建微博
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


    //删除微博
  public function destroy(Status $status){
      //权限验证
    $this->authorize('destroy',$status);
    $status->delete();
    session()->flash('success','删除成功');
    return redirect()->back();
  }
}
