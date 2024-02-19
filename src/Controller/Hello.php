<?php

namespace App\Controller;

use App\Common\Http\ResponseHelper;
use Mix\Vega\Context;

class Hello
{

    /**
     * @param Context $ctx
     */
    public function index(Context $ctx)
    {
        ResponseHelper::stringSuccess($ctx, 'hello, world!');
    }

}
