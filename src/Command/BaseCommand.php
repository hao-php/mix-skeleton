<?php

namespace App\Command;

use Haoa\Cli\Arguments;
use Haoa\Cli\BaseRun;
use Haoa\Cli\Flag;
use Haoa\Cli\FlagValue;
use function Swoole\Coroutine\run;

abstract class BaseCommand extends BaseRun
{

    /** @var bool 是否在协程环境中执行脚本 */
    protected bool $coroutine = true;

    abstract function handle(): void;

    public function optMatch(string ...$name): FlagValue
    {
        return Flag::match(...$name);
    }

    public function arg(): Arguments
    {
        return Flag::arguments();
    }

    function main(): void
    {
        if (!$this->coroutine) {
            $this->handle();
            return;
        }

        run(function () {
            \Swoole\Runtime::enableCoroutine();
            $this->handle();
        });
    }
}