<?php

namespace BrandStudio\Bonus\Facades;

use Illuminate\Support\Facades\Facade;

class Bonus extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'brandstudio_bonus';
    }

}
