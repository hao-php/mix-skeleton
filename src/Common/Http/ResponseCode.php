<?php

namespace App\Common\Http;

class ResponseCode
{

    const SUCCESS = ['code' => 0, 'msg' => 'success'];

    const CLIENT_ERROR = ['code' => 4000, 'msg' => '请求失败'];

    const LOGIN_ERROR = ['code' => 40001, 'msg' => '登录已失效，请重新登录'];

    const PARAMS_ERROR = ['code' => 40002, 'msg' => '参数错误'];

    const ACCESS_LIMIT = ['code' => 40003, 'msg' => '您的请求太频繁了，请稍后再试'];

    const SERVER_ERROR = ['code' => 50000, 'msg' => '服务器开小差了，请稍后再试'];


}