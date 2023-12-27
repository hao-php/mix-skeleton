<?php

namespace App\Container;

use App\Once;
use Haoa\Util\Util;
use Mix\Redis\Redis;

class RDS
{

    /**
     * @var Redis
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
     * @return Redis
     */
    public static function instance(): Redis
    {
        if (!isset(self::$instance)) {
            try {
                static::$once->do(function () {
                    $host = $_ENV['REDIS_HOST'];
                    $port = $_ENV['REDIS_PORT'];
                    $password = $_ENV['REDIS_PASSWORD'];
                    $database = $_ENV['REDIS_DATABASE'];
                    $rds = new Redis($host, $port, $password, $database);
                    APP_DEBUG and $rds->setLogger(new RDSLogger());
                    self::$instance = $rds;

                    if (Util::isCoroutine()) {
                        $maxOpen = 100;        // 最大开启连接数
                        $maxIdle = 10;        // 最大闲置连接数
                        $maxLifetime = 3600;  // 连接的最长生命周期
                        $waitTimeout = 10;   // 从池获取连接等待的时间, 0为一直等待
                        self::$instance->startPool($maxOpen, $maxIdle, $maxLifetime, $waitTimeout);
                    }
                });
            } catch (\Throwable $e) {
                self::$instance = null;
                static::$once->reset();
                throw $e;
            }
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

RDS::init();
