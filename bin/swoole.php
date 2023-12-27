#!/usr/bin/env php
<?php
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('memory_limit', '1G');

require __DIR__ . '/../vendor/autoload.php';

use App\Error;
use App\Container\Logger;
use App\Vega;
use Dotenv\Dotenv;

Dotenv::createUnsafeImmutable(__DIR__ . '/../', '.env')->load();
define("APP_DEBUG", env('APP_DEBUG'));

Error::register();

/**
 * 多进程默认开启了协程
 * 关闭协程只需关闭 `enable_coroutine` 配置并注释数据库的 `::enableCoroutine()` 即可退化为多进程同步模式
 */

$vega = Vega::new();
$host = '0.0.0.0';
$port = 9501;
$http = new Swoole\Http\Server($host, $port, SWOOLE_PROCESS); // SWOOLE_PROCESS | SWOOLE_BASE
$file = __FILE__;
$http->on('Request', $vega->handler());

$http->on('Start', function () use ($file) {
    try {
        swoole_set_process_name("php {$file} master");
    } catch (\Throwable $ex) {
        Error::handle($ex);
    }
});

$http->on('ManagerStart', function () use ($file) {
    try {
        swoole_set_process_name("php {$file} manager");
    } catch (\Throwable $ex) {
        Error::handle($ex);
    }
});

$http->on('WorkerStart', function ($server, $workerId) use ($file) {
    // swoole 协程不支持 set_exception_handler 需要手动捕获异常
    try {
        \Swoole\Runtime::enableCoroutine(); // hook all
        swoole_set_process_name("php {$file} worker");
    } catch (\Throwable $ex) {
        Error::handle($ex);
    }
});

$workerNum = swoole_cpu_num();
$http->set([
    'enable_coroutine' => true,
    'worker_num' => $workerNum,
    'max_request'   => 100000,
    'max_wait_time' => 30,
    'socket_dns_timeout' => 10,
    'socket_connect_timeout' => 10,
    'socket_write_timeout' => 60,
    'socket_read_timeout' => 60,
    'max_coroutine' => 100000,
    'enable_deadlock_check' => true,
]);

echo <<<EOL
                              ____
 ______ ___ _____ ___   _____  / /_ _____
  / __ `__ \/ /\ \/ /__ / __ \/ __ \/ __ \
 / / / / / / / /\ \/ _ / /_/ / / / / /_/ /
/_/ /_/ /_/_/ /_/\_\  / .___/_/ /_/ .___/
                     /_/         /_/


EOL;
printf("System    Name:       %s\n", strtolower(PHP_OS));
printf("PHP       Version:    %s\n", PHP_VERSION);
printf("Swoole    Version:    %s\n", swoole_version());
printf("Swoole    WorkerNum:  %s\n", $workerNum);
printf("Listen    Addr:       http://%s:%d\n", $host, $port);
printf("App       Debug:      %s\n", env("APP_DEBUG") ? 'true' : 'false');
Logger::instance()->info('Start swoole server');

$http->start();
