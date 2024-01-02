<?php

namespace App\Common\Util\SingleTon;

use App\Container\RunContext;

trait ContextSingleTonTrait
{

    public static function getInstance(): static
    {
        $key = '_ContextSingleTonTrait:' . static::class;
        $obj = RunContext::instance()->get($key);
        if (!empty($obj)) {
            return $obj;
        }
        $obj = new static();
        RunContext::instance()->set($key, $obj);
        return $obj;
    }

}