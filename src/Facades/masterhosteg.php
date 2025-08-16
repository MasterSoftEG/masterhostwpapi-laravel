<?php

namespace masterhosteg\Facades;

use Illuminate\Support\Facades\Facade;

class masterhosteg extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'masterhosteg.client';
    }
} 