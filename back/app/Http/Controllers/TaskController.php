<?php 
 
namespace App\Http\Controllers; 
 
use Illuminate\Http\Request; 
 
use App\Models\Task; 
use App\Models\Project; 

use Illuminate\Support\Facades\DB;
use Response;


class TaskController extends Controller 
{ 
    public function all_tasks(Request $request){ 
        $filter = $request->filter != '' && $request->filter != null ? $request->filter: false;

        if($request->role == 'manager'){
            // $tasks_all = 
            // DB::table('tasks')
            // ->select('tasks.id as tasks_id', 'projects.id as project_id','tasks.title as title_task','projects.title as title_project','users.name as worker','tasks.priority','tasks.finished_at', 'tasks.status')
            // ->where('manager_id','=',$request->id_user)
            // ->join('projects','projects.id','=','tasks.project_id')
            // ->join('users','users.id','=','tasks.user_id')
            // ->orderBy('tasks.updated_at','desc')
            // ->limit(9)
            // ->get(); 
            // $count = Task::latest()->where('manager_id','=',$request->id_user)->count();
            $tasks_all = 
            DB::table('tasks')
            ->select('tasks.id as tasks_id', 'projects.id as project_id','tasks.title as title_task','projects.title as title_project','users.name as worker','tasks.priority','tasks.finished_at', 'tasks.status', 'comments')
            ->where('manager_id','=',$request->id_user);
            $count = Task::latest()->where('manager_id','=',$request->id_user);
            if($filter){
                $tasks_all = $tasks_all->where('priority', '=', $filter); 
                $count = $count->where('priority', '=', $filter);
            }
            $tasks_all = $tasks_all->join('projects','projects.id','=','tasks.project_id')
                ->join('users','users.id','=','tasks.user_id')
                ->orderBy('tasks.updated_at','desc')
                ->limit(9)
                ->get(); 
            $count = $count->count();
        }
        else if($request->role=='admin'){
            $tasks_all = 
            DB::table('tasks')
            ->select('tasks.id as tasks_id', 'projects.id as project_id','tasks.title as title_task','projects.title as title_project','users.name as worker','tasks.priority','tasks.finished_at', 'tasks.status', 'comments')
            ->join('projects','projects.id','=','tasks.project_id')
            ->join('users','users.id','=','tasks.user_id')
            ->orderBy('tasks.updated_at','desc')
            ->limit(9)
            ->get();    
            $count = Task::latest()->count();
        }
        // if(){

        // }
        return response()->json(['filter'=>$filter,'tasks'=>$tasks_all, 'count'=>$count]); 
    }
    public function get_tasks_on_page(Request $request){ 
        $page = $request->page_id; 
        $offset = $page*9-9; 
        $filter = $request->filter != null && $request->filter !='' ? $request->filter : false;
        
        if($request->role == 'manager'){
            $tasks = DB::table('tasks')
            ->select('tasks.id as tasks_id', 'projects.id as project_id','tasks.title as title_task','projects.title as title_project','users.name as worker','tasks.priority','tasks.finished_at', 'tasks.status', 'comments')
            ->where('manager_id','=',$request->id_user);
            $count = DB::table('tasks')->where('manager_id','=',$request->id_user);
            if($filter){
                $tasks = $tasks->where('priority', '=', $filter);
                $count = $count->where('priority', '=', $filter);
            }
            $tasks = $tasks->join('projects','projects.id','=','tasks.project_id')
            ->join('users','users.id','=','tasks.user_id')
            ->orderBy('tasks.updated_at','desc')
            ->limit(9)
            ->offset($offset)
            ->get();            
            $count = $count->count();
            return response()->json(['filter'=>$filter,'tasks'=>$tasks,'count'=>$count, 'offset'=>$offset, 'page'=>$page]);
            // return response()->json($tasks); 
        }
        else if($request->role == 'admin'){
                $tasks = DB::table('tasks')
                ->select('tasks.id as tasks_id', 'projects.id as project_id','tasks.title as title_task','projects.title as title_project','users.name as worker','tasks.priority','tasks.finished_at', 'tasks.status', 'comments')
                ->join('projects','projects.id','=','tasks.project_id')
                ->join('users','users.id','=','tasks.user_id')
                ->orderBy('tasks.updated_at','desc')
                ->limit(9)
                ->offset($offset)
                ->get(); 
                $count = DB::table('tasks')->count();
           
            return response()->json(['tasks'=>$tasks,'count'=>$count, 'offset'=>$offset, 'page'=>$page]);
        }
        else if($request->role == 'worker'){
            // $workers_task = DB::table('tasks')->select('title', 'started_at', 'finished_at', 'priority', 'status', 'comments')->where('user_id','=', $request->id_worker)->get();
            $workers_task = DB::table('tasks')->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')
            ->where('user_id','=', $request->id_worker);
            // ->orderBy('tasks.updated_at','desc')
            $count = DB::table('tasks')->where('user_id','=', $request->id_worker);
            if($filter != '' & $filter != null){
                $workers_task = $workers_task->where('priority','=', $filter);
                $count = $count->where('priority','=', $filter);;
            }
            $workers_task = $workers_task->limit(9)
            ->offset($offset)
            ->get();
            $count = $count->count();
            
            // $tasks = DB::table('tasks')
            //     // ->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')    
            //     ->join('projects','projects.id','=','tasks.project_id')
            //     ->join('users','users.id','=','tasks.user_id')
            //     // ->where('user_id','=', $request->id_worker)
            //     ->orderBy('tasks.updated_at','desc')
            //     ->limit(9)
            //     ->offset($offset)
            //     ->get(); 
            // $count = DB::table('tasks')->where('user_id','=', $request->id_worker)->count();
            return response()->json(['filter'=>$request->filter, 'page_id'=>$request->page_id,'tasks'=>$workers_task,'count'=>$count, 'offset'=>$offset, 'page'=>$page]);

            // $tasks = DB::table('tasks')
            // ->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')
            // ->where('user_id','=', $request->id_worker)
            // // ->orderBy('tasks.updated_at','desc')
            // ->limit(9)
            // ->offset($offset)
            // ->get();
            // return response()->json(['tasks'=>$tasks]);
            
            
            // // $workers_task = DB::table('tasks')->select('title', 'started_at', 'finished_at', 'priority', 'status', 'comments')->where('user_id','=', $request->id_worker)->get();
            // $workers_task = DB::table('tasks')->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')
            // ->where('user_id','=', $request->id_worker)
            // // ->orderBy('tasks.updated_at','desc')
            // ->limit(9)
            // ->offset($offset)
            // ->get();
            // $count = DB::table('tasks')->where('user_id','=', $request->id_worker)->count();
            // if($filter != false){
            //     $workers_task = $workers_task->where('priority','=', $filter);
            //     $count = $count->where('priority','=', $filter);
            // }
            // // $tasks = DB::table('tasks')
            // //     // ->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')    
            // //     ->join('projects','projects.id','=','tasks.project_id')
            // //     ->join('users','users.id','=','tasks.user_id')
            // //     // ->where('user_id','=', $request->id_worker)
            // //     ->orderBy('tasks.updated_at','desc')
            // //     ->limit(9)
            // //     ->offset($offset)
            // //     ->get(); 
            // // $count = DB::table('tasks')->where('user_id','=', $request->id_worker)->count();
            // return response()->json(['filter'=>$filter,'tasks'=>$workers_task,'count'=>$count, 'offset'=>$offset, 'page'=>$page,]);

            // // $tasks = DB::table('tasks')
            // // ->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')
            // // ->where('user_id','=', $request->id_worker)
            // // // ->orderBy('tasks.updated_at','desc')
            // // ->limit(9)
            // // ->offset($offset)
            // // ->get();
            // // return response()->json(['tasks'=>$tasks]); 
        }
        // return response()->json($request); 
        
    } 
    public function get_manager_of_project(Request $request){ 
        $manager_p = DB::table('projects')->select('users.name')->where('users.id','projects.user.id')-> 
        join('users', 'users.id', 'projects.user_id', '=', 'users.id')->get(); 
        return response()->json($manager_p); 
    } 
    public function one_task_more(Request $request){ 
        $task_info = DB::table('tasks')-> 
        join('users', 'users.id', '=', 'tasks.user_id')-> 
        select('users.name', 'tasks.id', 'tasks.title', 'tasks.description', 'tasks.project_id')-> 
        where('tasks.id', $request->id)->get(); 
        $boss = DB::table('users')->join('projects', 'users.id', '=', 'projects.user_id')-> 
        where('projects.id',$task_info[0]->project_id)-> 
        select('users.name')->get(); 
        $info_task_boss = ['task_worker'=>$task_info[0], 'boss'=>$boss[0]]; 
        return response()->json($info_task_boss); 
    } 
    public function get_info_form_create_task(Request $request){
        $id_project = (int) substr($request->project_id, 11);
        $squad = (array) json_decode(DB::table('projects')->select('squad')->where('id','=', $id_project)->get()[0]->squad);
        $squad = $squad['squad'];
        $arr_from_n_worker = [];
        foreach($squad as $worker_id){
            $worker = DB::table('users')->select('id','name')->where('id','=',$worker_id)->get()[0];
            $arr_from_n_worker[$worker->id] = $worker->name;
        }
        $project_info = DB::table('projects')->select('id','title','started_at', 'finished_at')->where('id','=', $id_project)->get();
        return response()->json(['info'=>$project_info,'arr'=>$arr_from_n_worker,'request'=>$request]);
    }
    public function save_create_task(Request $request){
        
        $id_proj =isset($request->id_project) ? $request->id_project: false;
        $title = (strlen(trim($request->title))>0)? $request->title : false;
        $desc = (strlen(trim($request->description))>0) ? $request->description : false;
        $start = isset($request->started_at) ? $request->started_at: false;
        $finish = isset($request->finished_at) ? $request->finished_at: false;
        $worker = isset($request->worker) ? $request->worker: false;
        $priority = isset($request->priority) ? $request->priority: false;
        $manager_id = isset($request->manager_id)?$request->manager_id:false;
        if($id_proj && $title && $desc && $start && $finish && $worker && $worker && $priority){
            $task = Task::create([
                'title'=>$title,
                'description'=>$desc,
                'project_id'=>$id_proj,
                'started_at'=>$start,
                'finished_at'=>$finish,
                'user_id'=>$worker,
                'priority'=>$priority,
                'manager_id'=>$manager_id,
                'status'=>'Назначена'
            ]);
            if($task){
                $mess = 'Успешное создание задачи!';
                $res = true;
                $tasks_all = DB::table('tasks')
                ->select('tasks.id as tasks_id', 'projects.id as project_id','tasks.title as title_task','projects.title as title_project','users.name as worker','tasks.priority','tasks.finished_at', 'tasks.status')
                ->join('projects','projects.id','=','tasks.project_id')
                ->join('users','users.id','=','tasks.user_id')
                ->where('manager_id', '=', $request->manager_id)
                ->orderBy('tasks.updated_at','desc')
                ->limit(9)
                ->offset(0)
                ->get();
                // return response()->json([]); 
            }   
            else{
                $mess = 'Не удалось назначить задачу!';
                $res = false;
            }
        }
        else{
            $mess = 'Заполните все поля!';
            $res =false;
        }
        return response()->json(['mess'=>$mess, 'res'=>$res, 'tasks'=>$tasks_all, 'count'=>Task::latest()->where('manager_id', '=', $request->manager_id)->count()]);
        // return response()->json($request);
        // return response()->json(['id_p'=>$id_proj,
        // 'tit'=>$title,
        // 'desc'=>$desc,
        // 'st'=>$start,
        // 'fsh'=>$finish, 
        // 'wr'=>$worker,
        // 'pr'=>$priority,
        // 'mn_id'=>$manager_id, $res]);
    }
    public function get_info_to_edit_task(Request $request){
        $info_task = DB::table('tasks')->select('id','title','description','started_at','finished_at','user_id', 'priority', 'project_id')->where('id','=', $request->id_task)->get();
        $dates = DB::table('projects')->select('started_at','finished_at')->where('id','=',$info_task[0]->project_id)->get();
        return response()->json(['info'=>$info_task[0], 'dates'=>$dates[0]]);
    }
    public function update_task(Request $request){
        $filter = $request->filter != '' && $request->filter != null ? $request->filter: false;
        $page_id = $request->page_id != '' && $request->page_id != null ? $request->page_id: false;
        $id_task =isset($request->id_task) ? $request->id_task: false;
        $title = (strlen(trim($request->title))>0)? $request->title : false;
        $desc = (strlen(trim($request->description))>0) ? $request->description : false;
        $start = isset($request->started_at) ? $request->started_at: false;
        $finish = isset($request->finished_at) ? $request->finished_at: false;
        if($title && $desc && $start && $finish && $id_task){
            $task_update = Task::where('id', '=', $id_task)->update(['title'=>$title, 'description'=>$desc,'started_at'=>$start, 'finished_at'=>$finish, 'updated_at'=>date('Y-m-d H:i:s')]);
            if($task_update){
                $mess = 'Успешное изменение задачи!';
                $res = true;
                $tasks_all = DB::table('tasks')
                ->select('tasks.id as tasks_id', 'projects.id as project_id','tasks.title as title_task','projects.title as title_project','users.name as worker','tasks.priority','tasks.finished_at', 'tasks.status', 'comments')
                ->join('projects','projects.id','=','tasks.project_id')
                ->join('users','users.id','=','tasks.user_id'); 
                $count = Task::latest(); 
                if($filter){
                    $tasks_all = $tasks_all->where('priority', '=', $filter);
                    $count = $count->where('priority', '=', $filter);
                }
                $tasks_all = $tasks_all->orderBy('tasks.updated_at','desc')
                ->limit(9);
                // if($page_id){
                //     $offset = $page_id*9-9;
                //     $tasks_all = $tasks_all->offset($offset);
                // }
                $tasks_all = $tasks_all->get();
                $count = $count->count();
            }
            else{
                $mess = 'Не удалось изменить задачу!';
                $res = false;
            }
        }
        else{
            $mess = 'Заполните все поля!';
            $res = false;
        }
        return response()->json(['p.diddy'=>$page_id, 'filter'=>$filter,'mess'=>$mess, 'res'=>$res, 'tasks'=>$tasks_all, 'count'=>$count]);
    }
    public function delete_task(Request $request){
        $filter = $request->filter != '' && $request->filter != null ? $request->filter: false;
        $page_id = $request->page_id != '' && $request->page_id != null ? $request->page_id: false;
        $offset = $page_id*9-9;
        $delete_task = Task::where('id','=',$request->id_task)->delete();
        if($delete_task){
            $mess = 'Успешное удаление задачи!';
            $res = true;
        }
        else{
            $mess = 'Не удалось удалить задачу!';
            $res = false;
        }
        $tasks_all = DB::table('tasks')
        ->select('tasks.id as tasks_id', 'projects.id as project_id','tasks.title as title_task','projects.title as title_project','users.name as worker','tasks.priority','tasks.finished_at', 'tasks.status', 'comments')
        ->join('projects','projects.id','=','tasks.project_id')
        ->join('users','users.id','=','tasks.user_id')
        ->where('manager_id', '=', $request->id_manager);
        $count = Task::latest()->where('manager_id', '=', $request->id_manager);
        if($filter){
            $tasks_all = $tasks_all->where('priority', '=', $filter);
            $count = $count->where('priority', '=', $filter);
        }
        $tasks_all = $tasks_all->orderBy('tasks.updated_at','desc')
        ->limit(9)
        ->offset($offset)
        ->get(); 
        $count = $count->count();
        return response()->json(['res'=>$res, 'mess'=>$mess,'tasks'=>$tasks_all, 'count'=>$count]);
    }

    public function tasks_worker(Request $request){
        $limit = $request->page_id;
        $offset = $limit*9-9;
        
        $workers_task = DB::table('tasks')->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')->where('user_id','=', $request->id_worker);
        $count = DB::table('tasks')->where('user_id','=', $request->id_worker);
        if($request->filter != '' && $request->filter != null){
            $workers_task = $workers_task->where('priority','=', $request->filter);
            $count = $count->where('priority','=', $request->filter);
        }
        $workers_task = $workers_task->limit(9)->offset($offset)->get();
        $count = $count->count();
        return response()->json(['filter'=>$request->filter,'limit'=>$limit,'tasks'=>$workers_task, 'count'=>$count]);
    }
    public function get_comments_for_task(Request $request){
        $task = DB::table('tasks')->select('id','comments', 'title')->where('id', '=', $request->id_task)->get()[0];
        $comments = $task->comments;
        $title = $task->title;
        $id = $task->id;
        return response()->json(['comments'=>$comments, 'title'=>$title, 'id_task'=>$id]);
    }
    public function create_comm(Request $request){
        $mess = 'Что-то пошло не так!';
        $res = false;
        $amount_comm = 0;
        $content_comment = $request->content_comment;
        $workers_task = false;
        $count = false;
        if($content_comment == null || strlen(trim($content_comment))<0){
            $mess = 'Напишите что-нибудь для создания комментария!';
        }else{
            $count_comms =DB::table('tasks')->select('comments')->where('id', '=', $request->id_task)->get()[0]->comments; 
            $current_date = date('Y-m-d H:i:s');
            $date_time = date("Y-m-d H:i:s", strtotime("$current_date +5 hours"));
            $new_json_comm = [];
                if($count_comms == null){
                    $new_json_comm[$date_time] = $content_comment;
                    $amount_comm = 1;
                }
                else{
                    $get_old_comms = json_decode(DB::table('tasks')->select('comments')->where('id', '=', $request->id_task)->get()[0]->comments);
                    foreach($get_old_comms as $old_date=>$old_content){
                        $new_json_comm[$old_date] = $old_content;
                        $amount_comm++;
                    }
                    $new_json_comm[$date_time] = $content_comment;
                    $amount_comm++;
                }
            $update_comm = Task::where('id', '=', $request->id_task)->update(['comments'=> $new_json_comm]);
            if($update_comm){
                $mess = 'Комментарий успешно создан!';
                $res = true;
                $task = DB::table('tasks')->select('comments')->where('id', '=', $request->id_task)->get()[0];
                $comments = $task->comments;
            }
            else{
                $mess = 'Не удалось создать комментарий!';
            }
            
        }
        return response()->json(['comments'=>$new_json_comm, 'amount'=>$amount_comm, 'mess'=>$mess, 'res'=>$res, 'id_task'=>$request->id_task]);
    }

    public function delete_comment(Request $request){
        $array_comms =json_decode(DB::table('tasks')->select('comments')->where('id','=',$request->id_task)->get()[0]->comments);
        $count = 0;
        $res = false;
        $new_comms = [];
        foreach($array_comms as $date=>$content){
            if($count != $request->id_comm){
                $new_comms[$date] = $content;
            }
            $count++;
        }
        $new_comms = json_encode($new_comms);
        $update = DB::table('tasks')->where('id','=',$request->id_task)->update(['comments'=>$new_comms]);
        if($update){
            $res = true;
            $mess = 'Комментарии обновлены!';
            $comm_render = DB::table('tasks')->select('comments')->where('id', '=', $request->id_task)->get()[0]->comments;
        }
        else{
            $mess = 'Не удалось обновить комментарии!';
        }
        return response()->json(['mess'=>$mess,'amount'=>$count-1, 'comments'=>$comm_render, 'res'=>$res, 'id_task'=>$request->id_task, 'act'=>'delete']);
        // return response()->json($array_comms);
        // return response()->json($request);
    }
    public function see_more_task_worker(Request $request){
        $id_task = $request->id;
        $task_info = DB::table('tasks')->select('title', 'users.name', 'description')->where('id','=', $id_task)->join('users', 'users.id','=','tasks.user_id')->get();
        $project_manager = DB::table('projects')->select('users.name')->where('', '=',$id_task)->join('users', 'users.id', '=', 'projects.user_id')->get();
        return response()->json(['task_worker'=>$task_info, 'boss'=>$project_manager]);
    }
    public function change_status(Request $request){
        $filter =  $request->filter != '' && $request->filter != null ? $request->filter : false;
        $today = date('Y-m-d');
        $change = Task::where('id', '=', $request->id_task)->update(['status'=>$request->status, 'finished_at'=>$today]);
        if($change){
            $res = true;
            $project_id = DB::table('tasks')->where('id','=', $request->id_task)->select('project_id')->get()[0]->project_id;
            $status_project = DB::table('projects')->where('id', '=', $project_id)->select(columns: 'status')->get()[0]->status;
            if($status_project == 'Создан'){
                $update_status_project = Project::where('id', '=',$project_id)->update(['status'=>'В процессе']);
            }
            
            $mess = 'Успешное изменение статуса!';
            if(!isset($request->page) || $request->page == 1){
                $workers_task = DB::table('tasks')->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')->where('user_id','=', $request->id_worker);
                $count = DB::table('tasks')->where('user_id','=', $request->id_worker);
                if($filter){
                    $workers_task = $workers_task->where('priority', '=', $filter);
                    $count = $count->where('priority', '=', $filter);                
                }
                $workers_task = $workers_task->limit(9)->get();
                $count = $count->count();
            }
            else{
                $offset = $request->page*9-9;
                $workers_task = DB::table('tasks')->select('id','title', 'started_at', 'finished_at', 'priority', 'status', 'comments')->where('user_id','=', $request->id_worker);
                $count = DB::table('tasks')->where('user_id','=', $request->id_worker);
                if($filter){
                    $workers_task = $workers_task->where('priority', '=', $filter);
                    $count = $count->where('priority', '=', $filter);                
                }
                $workers_task = $workers_task->limit(9)->offset($offset)->get();
                $count = $count->count();
            }
            
        }
        else{
            $res = true;
            $mess = 'Не удалось изменить статус!';
        }
        
        return response()->json([ 'status'=>$status_project, 'proj'=>$project_id,'mess'=>$mess, 'res'=>$res, 'tasks'=>$workers_task, 'count'=>$count]);
    }
}