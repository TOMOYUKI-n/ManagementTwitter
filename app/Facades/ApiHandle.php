<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ApiHandle extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'apihandle';
    }
}