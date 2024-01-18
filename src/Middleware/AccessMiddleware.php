<?php

namespace App\Middleware;

use App\Container\Logger;
use Mix\Vega\Context;

class AccessMiddleware
{

    protected static $controllers = [];

    /**
     * @return \Closure
     */
    public static function callback(): \Closure
    {
        return function (Context $ctx) {
            if (APP_DEBUG) {
                Logger::instance()->debug();
            }
            $ctx->next();
        };
    }
}