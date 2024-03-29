<?php

namespace App\Container;

use App\Once;
use Haoa\Util\Util;
use Haoa\MixExt\Db\Database;

class DB
{

    /**
     * @var Database
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
     * @return Database
     */
    public static function instance(): Database
    {
        if (!isset(self::$instance)) {
            static::$once->do(function () {
                try {
                    $dsn = $_ENV['DATABASE_DSN'];
                    $username = $_ENV['DATABASE_USERNAME'];
                    $password = $_ENV['DATABASE_PASSWORD'];
                    $db = new Database($dsn, $username, $password, [
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_TIMEOUT => 30,
                        // \PDO::ATTR_EMULATE_PREPARES => false
                    ]);
                    APP_DEBUG and $db->setLogger(new DBLogger());
                    self::$instance = $db;

                    if (Util::isCoroutine()) {
                        $maxOpen = 100;        // 最大开启连接数
                        $maxIdle = 10;        // 最大闲置连接数
                        $maxLifetime = 3600;  // 连接的最长生命周期
                        $waitTimeout = 10;   // 从池获取连接等待的时间, 0为一直等待
                        self::$instance->startPool($maxOpen, $maxIdle, $maxLifetime, $waitTimeout);
                    }
                } catch (\Throwable $e) {
                    self::$instance = null;
                    static::$once->reset();
                    throw $e;
                }
            });
        }
        return self::$instance;
    }

    /**
     * @return void
     */
    public static function enableCoroutine(): void
    {
        $maxOpen = 30;        // 最大开启连接数
        $maxIdle = 10;        // 最大闲置连接数
        $maxLifetime = 3600;  // 连接的最长生命周期
        $waitTimeout = 0.0;   // 从池获取连接等待的时间, 0为一直等待
        self::instance()->startPool($maxOpen, $maxIdle, $maxLifetime, $waitTimeout);
    }

}

DB::init();
