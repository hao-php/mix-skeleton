<?php

namespace App\Controller;

use App\Common\Context\ContextConst;
use Mix\Vega\Context;

class Dispatch
{

    public function run(Context $ctx)
    {
        $controller = $ctx->mustGet(ContextConst::HTTP_CONTROLLER_OBJ);
        $action = $ctx->mustGet(ContextConst::HTTP_ACTION_NAME);
        return call_user_func([$controller, $action], $ctx);
    }

}