<?php

namespace App\Common\Exception;

use App\Container\Logger;

class ExceptionHelper
{

    public static function handle(\Throwable $ex) {
        Logger::instance()->error($ex->__toString());
    }

}