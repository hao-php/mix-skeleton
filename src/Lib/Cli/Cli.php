<?php

namespace App\Lib\Cli;

/**
 * Class Cli
 */
class Cli extends \Mix\Cli\Cli
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
        static::$app = new Application('app', '0.0.0');
    }

    /**
     * @return Application
     */
    public static function addArrCommand(array ...$commands): Application
    {
        return static::$app->addArrCommand(...$commands);
    }

}
