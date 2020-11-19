<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Validator;
use App\Http\Models\Admin;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;

class AuthController extends BaseController
{

    public function showAdminLoginPage()
    {
        return view('auth.login');

    }

    public function doAdminLogin()
    {

        $validation = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            session()->flash('error_msg', 'Please enter valid information');
            return redirect()->route('auth.login');
        }

        $email = request('email');
        $password = request('password');

        $admin = Admin::where('email', $email)->first();

        if (!isset($admin)) {
            session()->flash('error_msg', 'User not found');
            return redirect()->route('auth.login');
        }

        if (Hash::check($password, $admin->password)) {

            session()->put('admin', $admin);

        }
        return redirect('/');
    }

    public function logout() {
        session()->remove('admin');
        return redirect('/');
    }

}
