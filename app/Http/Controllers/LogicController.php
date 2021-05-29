<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\MyConstants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LogicController extends Controller
{

    //LOGIN
    public function login(Request $request)
    {
        $message = "User doesn't exist";
        $email = $request->get('email');
        $password = md5($request->get('password'));
        try {
            $user = \DB::table("users")
                ->where("email", "=", $email)
                ->where("password", "=", $password)
                ->first();


            if($user) {
                $request->session()->put("user", $user);
                $data = new \stdClass();

                $data->id = $user->id;

                return response()->json($data);

            }
            else
            {
                $data = new \stdClass();
                $data->message = $message;
                return response(json_encode($data),422);

            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response(json_encode($e->getMessage()),500);
        }
    }

    //LOGOUT
    public function logout(Request $request) {
        $request->session()->remove("user");
    }

    //REGISTER
    public function register (RegisterRequest $request) {
        $email = $request->get('email');
        $password = md5($request->get('password_confirmation'));
        try {
            $user = new User();
            $user->register($request);


            $user = \DB::table("users")
                ->where("email", "=", $email)
                ->where("password", "=", $password)
                ->first();

            if($user) {
                $request->session()->put("user", $user);
                $data = new \stdClass();

                $data->id = $user->id;

                return response(json_encode("User registered"),201);
            }

            return response(json_encode("User registered"),201);
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return response(json_encode($e->getMessage()),500);
        }
    }

    //ONLOAD GET
    public function index(Request $request)
    {
        $task = new Task();
        $items = $task->get($request);
        return response()->json($items);
    }

    //STORE TASK
    public function store(StoreTaskRequest $request)
    {
        try {
            $model = new Task();

            //$model->store($request);
            //$items = $model->where('user_id',$request->session()->get('user')->id)->paginate(MyConstants::PAGINATE_PER_PAGE);

            $items = $model->store($request);
            return response(json_encode($items),201);
        }
        catch(\Exception $ex) {
            \Log::error($ex->getMessage());
            return response()->json($ex->getMessage());
        }


    }

    public function show($id)
    {
        //
    }

    //SHOW EDIT FORM
    public function edit($id)
    {
        $task = new Task();
        $items = $task->where("id",$id)->first();
        return response()->json($items);
    }


    //UPDATE TASK
    public function update(StoreTaskRequest $request, $id)
    {
        try {
            $model = new Task();

            //$model->updateTask($request, $id);
            //$items = $model->where('user_id',$request->session()->get('user')->id)->paginate(MyConstants::PAGINATE_PER_PAGE);

            $items = $model->updateTask($request, $id);
            return response()->json($items);

        } catch(\Exception $ex) {
            \Log::error($ex->getMessage());
            return response()->json($ex->getMessage());
        }
    }

    //DELETE TASK
    public function destroy(int $id, Request $request)
    {
        $model = new Task();
        $model->where("id", "=",$id)->delete();

        $items = $model->where('user_id','=',$request->session()->get('user')->id)->paginate(MyConstants::PAGINATE_PER_PAGE);

        return response()->json($items);
    }
}
