<?php

namespace App\Controller;

use App\Common\Const\VegaContextConst;
use Mix\Vega\Context;

class Dispatch
{

    public function run(Context $ctx)
    {
        $controller = $ctx->mustGet(VegaContextConst::HTTP_CONTROLLER_OBJ);
        $action = $ctx->mustGet(VegaContextConst::HTTP_ACTION_NAME);
        return call_user_func([$controller, $action], $ctx);
    }

}