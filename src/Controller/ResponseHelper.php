<?php

namespace App\Controller;

use App\Common\Const\VegaContextConst;
use App\Container\Logger;
use Mix\Vega\Context;

class ResponseHelper
{


    public static function jsonError(Context $ctx, $code, $msg = '', object|null $data = null)
    {
        $data = [
            'code' => $code,
            'msg' => $msg,
            'data' => (object)$data
        ];
        $ctx->JSON(200, $data);
        $ctx->abort();
    }

    public static function jsonSuccess(Context $ctx, object|null $data = null)
    {
        $ctx->set(VegaContextConst::HTTP_RESPONSE_CALLBACK, function ($ctx) use ($data) {
            $data = [
                'code' => ResponseCode::SUCCESS['code'],
                'msg' => ResponseCode::SUCCESS['msg'],
                'data' => (object)$data
            ];
            $ctx->JSON(200, $data);
        });
    }

    public static function stringSuccess(Context $ctx, string $content)
    {
        $ctx->set(VegaContextConst::HTTP_RESPONSE_CALLBACK, function ($ctx) use ($content) {
            $ctx->string(200, $content);
        });
    }

}