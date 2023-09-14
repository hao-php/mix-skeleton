<?php

namespace App\Command;

use App\Lib\Cli\BaseRun;

class CoroutineRun extends BaseRun
{

    public function main(): void
    {
        $func = function () {
            // do something
        };
        \Swoole\Coroutine\run(function () use ($func) {
            \Swoole\Runtime::enableCoroutine();
            $func();
        });
    }

}
