<?php

namespace App\Common\Log;

use App\Container\Logger;

/**
 * @method static debug(string|\Stringable $message, array $context = [], string $cate = '')
 * @method static info(string|\Stringable $message, array $context = [], string $cate = '')
 * @method static notice(string|\Stringable $message, array $context = [], string $cate = '')
 * @method static warning(string|\Stringable $message, array $context = [], string $cate = '')
 * @method static error(string|\Stringable $message, array $context = [], string $cate = '')
 */
class LoggerHelper
{

    public static function __callStatic($name, $arguments)
    {
        if (!empty($arguments[2])) {
            $arguments[1]['cate'] = $arguments[2];
        }
        call_user_func_array([Logger::instance(), $name], $arguments);
    }
}