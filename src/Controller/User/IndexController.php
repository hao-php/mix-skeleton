<?php

namespace App\Controller\User;

use Mix\Vega\Context;

class IndexController
{

    public function hello(Context $ctx)
    {
        $ctx->string(200, "hello user");
    }

}