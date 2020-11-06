<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyList;
class ToDoListController extends Controller
{
    //展示页面
    public function index(){
        //获取数据库中所有的数据
        $data = MyList::all();
        return view('todolist.todo',compact('data'));
    }

    //添加任务
    public function create(Request $request){
        $data= $request->only('content');
        $res = MyList::create($data);
        return ['code'=>200,'data'=>$res];
    }

    //删除任务
    public function destroy(Request $request){
//        $data = MyList::find($request->get('id'));
//        $data->delete();
        MyList::destroy($request->get('id'));
//        dump($request->get('id'));
        return ['code'=>200,'msg'=>'删除成功'];
    }

    //完成任务
    public function change(Request $request){
        $id = $request->get('id');
        $res = MyList::find($id);
        $res->status = $res->status? 0 : 1;
        $res->save();
        return ['code'=>200,'msg'=>'更改成功'];
    }


    public function status(Request $request){
        $data = MyList::where('status',$request->get('status'))->get();
        return $data;
    }
}
