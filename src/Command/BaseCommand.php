<?php

namespace App\Command;

use App\Common\Context\ContextConst;
use App\Container\RunContext;
use Co\Context;
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

    protected function optMatch(string ...$name): FlagValue
    {
        return Flag::match(...$name);
    }

    protected function arg(): Arguments
    {
        return Flag::arguments();
    }

    protected function buildTraceId()
    {
        return "cli_" . session_create_id();
    }

    function main(): void
    {
        if (!$this->coroutine) {
            RunContext::instance()->set(ContextConst::KEY_LOG_TRACE_ID, $this->buildTraceId());
            $this->handle();
            return;
        }

        run(function () {
            \Swoole\Runtime::enableCoroutine();
            RunContext::instance()->set(ContextConst::KEY_LOG_TRACE_ID, $this->buildTraceId());
            $this->handle();
        });
    }
}