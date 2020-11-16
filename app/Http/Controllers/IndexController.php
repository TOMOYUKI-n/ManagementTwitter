<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GetAPI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    //
    public function misslogin()
    {
        return view('misslogin');
    }
    public function top()
    {
        return view('top');
    }
    public function term()
    {
        return view('term');
    }
    public function policy()
    {
        return view('policy');
    }

    public function account() {
        return view('index.account');
    }

    public function dashboard() {
        return view('index.dashboard');
    }

}
