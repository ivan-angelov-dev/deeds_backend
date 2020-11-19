<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;

class PageController extends BaseController
{

    public function showFirstPage()
    {
        $admin = session()->get('admin');
        if (isset($admin)) {
            return redirect('/dashboard');
        }

        return redirect()->route('auth.login');

    }


}
