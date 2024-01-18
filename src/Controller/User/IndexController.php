<?php

namespace App\Controller\User;

use App\Controller\ResponseHelper;
use Mix\Vega\Context;

class IndexController
{

    public function hello(Context $ctx)
    {
//        $ctx->string(200, "hello user");
        ResponseHelper::jsonSuccess($ctx);
    }

}