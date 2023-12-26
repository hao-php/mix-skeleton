<?php

namespace App\Common\Cli;

/**
 * Class Cli
 */
class Cli
{

    /**
     * @var Application
     */
    protected static $app;

    /**
     * Init
     */
    public static function init(): void
    {
        if (PHP_SAPI != 'cli') {
            return;
        }
        static::$app = new Application();
    }

    /**
     * @return Application
     */
    public static function addArrCommand(array ...$commands): Application
    {
        return static::$app->addArrCommand(...$commands);
    }

}
