<?php

namespace App\Middleware;

use App\Common\Const\VegaContextConst;
use App\Container\Logger;
use Mix\Vega\Context;
use Mix\Vega\Exception\NotFoundException;

class UriMapMiddleware
{

    protected static $controllers = [];

    /**
     * @return \Closure
     */
    public static function callback(): \Closure
    {
        return function (Context $ctx) {
            $dir = ucfirst($ctx->param('dir'));
            $controller = ucfirst($ctx->param('controller')) ?: 'Index';
            $action = ucfirst($ctx->param('action'));
            var_dump($ctx->uri()->getPath());

            $controllerPath = "App\\Controller\\{$dir}\\{$controller}Controller";

            if (isset(self::$controllers[$controllerPath])) {
                $controllerObj = self::$controllers[$controllerPath];
            } else {
                if (!class_exists($controllerPath)) {
                    Logger::instance()->debug(sprintf("404, path:%s  in %s on %s", $controllerPath, __FILE__, __LINE__));
                    throw new NotFoundException();
                }
                $controllerObj = new $controllerPath;
            }

            $ctx->set(VegaContextConst::HTTP_CONTROLLER_OBJ, $controllerObj);
//            $ctx->set(ContextConst::ACTION_PATH, strtolower("{$module}/{$dir}/{$controller}/{$action}"));
//            Logger::instance()->debug(sprintf("action_path %s", $ctx->get(ContextConst::ACTION_PATH)));

            if (!method_exists($controllerObj, $action)) {
                Logger::instance()->debug(sprintf("404 in %s on %s", __FILE__, __LINE__));
                throw new NotFoundException();
            }

            $ctx->set(VegaContextConst::HTTP_ACTION_NAME, $action);

            $ctx->next();
        };
    }
}