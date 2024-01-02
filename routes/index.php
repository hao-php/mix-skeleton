<?php

use App\Controller\Auth;
use App\Controller\Hello;
use App\Controller\Users;
use App\Middleware\AuthMiddleware;
use App\Middleware\UriMapMiddleware;
use App\Controller\Dispatch;

return function (Mix\Vega\Engine $vega) {
    $vega->handle('/hello', [new Hello(), 'index'])->methods('GET');
    $vega->handle('/users/{id}', /* AuthMiddleware::callback(), */ [new Users(), 'index'])->methods('GET');
    $vega->handle('/auth', [new Auth(), 'index'])->methods('GET');

    $vega->handle('/{dir}/{controller}/{action}',
        UriMapMiddleware::callback('api'),
        // AuthMiddleware::callback(),
        [new Dispatch(), 'run'])->methods('GET', 'POST', 'OPTIONS');

    $vega->handle('/{dir}/{action}',
        UriMapMiddleware::callback('api'),
        // AuthMiddleware::callback(),
        [new Dispatch(), 'run'])->methods('GET', 'POST', 'OPTIONS');
};
