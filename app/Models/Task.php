<?php

namespace App\Models;

use App\MyConstants;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Task extends Model
{
    use SoftDeletes;
    public function get(Request $request)
    {

        $query = \DB::table("tasks");

        /* JOINS */
        $query = $query->join("users", "tasks.user_id", "=", "users.id");

        /* WHERE */
        $query = $query->where("tasks.user_id","=",$request->session()->get("user")->id);
        $query = $query->whereNull('deleted_at');
        //$query = $query->whereNull('completed_at');


        if ($request->has("name")) {
            $query = $query->where("tasks.name", "like", "%" . $request->get("name") . "%");
        }
        if ($request->has("description")) {
            $query = $query->where("tasks.description", "like", "%" . $request->get("description") . "%");
        }
        if ($request->has("createDate")) {
            $query = $query->where("tasks.created_date", "=",  $request->get("createDate"));
        }
        if ($request->has("finishDate")) {
            $query = $query->where("tasks.complete_at", "=", $request->get("finishDate"));
        }
        if ($request->has("checkbox")) {
            if($request->get('checkbox'))
            {
                $query = $query->whereNotNull('completed_at');
            }
            else
            {
                $query = $query->whereNull('completed_at');
            }
        }


        /* SELECT */
        $query = $query->select("tasks.*", "tasks.name as Title", "tasks.description as Description", "tasks.created_date as Created", "tasks.complete_at as Finish at");
        //$query->paginate(4);
        return $query->paginate(MyConstants::PAGINATE_PER_PAGE);
    }
    public function store($request)
    {
            $name = $request->get('name');
            $description = $request->get("description");
            $completeAt = $request->get('date');


            $model = new Task();


            $model->name = $name;
            $model->description = $description;
            $model->created_date = Carbon::now()->format('Y-m-d');
            $model->complete_at = $completeAt;
            $model->user_id = $request->session()->get("user")->id;

            $model->save();

            return $model->where('user_id',$request->session()->get('user')->id)->paginate(MyConstants::PAGINATE_PER_PAGE);

    }
    public function updateTask($request, $id)
    {
        $check = $request->get('checkbox');
        $name = $request->get('name');
        $description = $request->get("description");
        $completeAt = $request->get('date');

        $model = new Task();
        $model = $model->find($id);
        $model->name = $name;
        if($check)
        {
            $model->completed_at = Carbon::now()->format('Y-m-d');
        }
        else
        {
            $model->completed_at = null;
        }
        $model->description = $description;
        $model->created_date = Carbon::now()->format('Y-m-d');
        $model->complete_at = $completeAt;
        $model->user_id = $request->session()->get("user")->id;

        $model->save();
        return $model->where('user_id',$request->session()->get("user")->id)->paginate(MyConstants::PAGINATE_PER_PAGE);
    }
}
