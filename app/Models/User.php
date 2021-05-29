<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    public function register($request){
        $user = new User();
            $user->email = $request->get('email');
            $user->password = md5($request->get('password'));
            $user->save();
    }
}
