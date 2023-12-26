<?php

namespace App\Common\Cli;

use Mix\Cli\RunInterface;
use function Swoole\Coroutine\run;

abstract class BaseRun implements RunInterface
{

    /** @var bool 是否在协程环境中执行脚本 */
    protected bool $coroutine = true;

    public array $options = [];

    abstract function handle(): void;

    public function main(): void
    {
        if (!$this->coroutine) {
            $this->handle();
            return;
        }

        run(function () {
            $this->handle();
        });
    }

}