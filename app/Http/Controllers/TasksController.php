<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;



class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $tasks = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];

            return view('tasks.index', [
                'tasks' => $tasks,
            ]);
          
           
        }
         // Welcomeビューでそれらを表示
            return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;

        // メッセージ作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // バリデーション
        $this->validate($request, [
            'status' => 'required|max:10',
        ]);
        
        $task = new Task;
        $task->status= $request->status;
        $task->content = $request->content;
        $task->user_id = \Auth::user()->id;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        if (\Auth::id() === $task->user_id) {
        $user = \Auth::user();
        
        // メッセージ詳細ビューでそれを表示
        return view('tasks.show', [
            'user' => $user,
            'task' => $task,
        ]);
        }
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        if (\Auth::id() === $task->user_id) {
        // メッセージ編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
        }
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // バリデーション
        $this->validate($request, [
            'status' => 'required|max:10',
        ]);
        
        $task = Task::findOrFail($id);
        if (\Auth::id() === $micropost->user_id) {
        $task->status = $request->status;
        $task->content = $request->content;
        $task->user_id = \Auth::user()->id;
        $task->save();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $task->user_id) {
        $task->delete();
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
