<?php

namespace App;

use App\Common\Const\VegaContextConst;
use App\Common\Exception\ExceptionHelper;
use App\Container\Logger;
use Haoa\Util\Context\RunContext;
use Mix\Vega\Abort;
use Mix\Vega\Context;
use Mix\Vega\Engine;
use Mix\Vega\Exception\NotFoundException;

class Vega
{

    /**
     * @return Engine
     */
    public static function new(): Engine
    {
        $vega = new Engine();

        // 500
        $vega->use(function (Context $ctx) {
            try {
                $ctx->next();

                call_user_func($ctx->get(VegaContextConst::HTTP_RESPONSE_CALLBACK), $ctx);
            } catch (\Throwable $ex) {
                if ($ex instanceof Abort || $ex instanceof NotFoundException) {
                    throw $ex;
                }
                Logger::instance()->error(sprintf('%s in %s on line %d', $ex->getMessage(), $ex->getFile(), $ex->getLine()));
                $ctx->string(500, 'Internal Server Error');
                $ctx->abort();
            } finally {
                try {
                    RunContext::destroy();
                } catch (\Throwable $e) {
                    ExceptionHelper::handle($e);
                    $ctx->abortWithStatus(500);
                }
            }
        });

        // 404
        $vega->use(function (Context $ctx) {
            try {
                $ctx->next();
            } catch (\Mix\Vega\Exception\NotFoundException $ex) {
                Logger::instance()->debug(sprintf('%s not found in %s on line %d', $ctx->uri()->getPath(), $ex->getFile(), $ex->getLine()));
                $ctx->string(404, '404 Not Found');
                $ctx->abort();
            }
        });

        // debug
        if (APP_DEBUG) {
            $vega->use(function (Context $ctx) {
                $ctx->next();
                Logger::instance()->debug(sprintf(
                    '%s|%s|%s|%s',
                    $ctx->method(),
                    $ctx->uri(),
                    $ctx->response->getStatusCode(),
                    $ctx->remoteIP()
                ));
            });
        }

        // routes
        $routes = require __DIR__ . '/../routes/index.php';
        $routes($vega);

        return $vega;
    }

}
