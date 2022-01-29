<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(){
        return view('auth.login');
    }
    public function addUser(){
        return view('admin.addUsers');
    }
    public function users(){
        $users = User::all();
        return view('admin.users',[
            'users'=>$users
        ]);
    }
    public function storeUsers(Request $request){
        $store = User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'phone'=>$request->phone,
            'role'=>$request->role,
            'password'=>Hash::make('password')
        ]);
        return redirect(url('users'))->with('success','USER ADDED SUCCESS');
    }

}
