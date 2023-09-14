<?php

namespace App\Container;

use App\Once;

/**
 * Class Config
 * @package App\Container
 */
class Config
{

    /**
     * @var \Noodlehaus\Config
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
     * @return \Noodlehaus\Config
     */
    public static function instance(): \Noodlehaus\Config
    {
        if (!isset(self::$instance)) {
            self::$once->do(function () {
                try {
                    self::$instance = new \Noodlehaus\Config(__DIR__ . '/../../conf');
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

Config::init();
