<?php

use App\User;
use App\Keyword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

