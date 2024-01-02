<?php

namespace App\Container;

use App\Once;
use Haoa\Util\Context\BaseContext;
use Haoa\Util\Context\ContextFactory;

class RunContext
{

    /**
     * @var BaseContext
     */
    private static $instance;

    /**
     * @var Once
     */
    private static $once;

    /**
     * @return void
     */
    public static function init(): void
    {
        self::$once = new Once();
    }

    /**
     * @return BaseContext
     */
    public static function instance(): BaseContext
    {
        if (!isset(self::$instance)) {
            static::$once->do(function () {
                try {
                    self::$instance = ContextFactory::getContext();
                } catch (\Throwable $e) {
                    self::$instance = null;
                    static::$once->reset();
                    throw $e;
                }
            });
        }
        return self::$instance;
    }

}

RunContext::init();