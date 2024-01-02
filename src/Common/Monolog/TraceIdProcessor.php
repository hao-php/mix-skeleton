<?php

namespace App\Lib\App\Monolog;

use App\Constant\RunContextKey;
use App\Container\RunContext;
use Monolog\Processor\ProcessorInterface;

class TraceIdProcessor implements ProcessorInterface
{


    /**
     * {@inheritDoc}
     */
    public function __invoke(array $record): array
    {
        $record['extra']['requestId'] = RunContext::instance()->get(RunContextKey::REQUEST_ID) ?? '';

        return $record;
    }

}