#!/usr/bin/env php
<?php
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('error_reporting', E_ALL ^ E_NOTICE);

require __DIR__ . '/../vendor/autoload.php';

use App\Error;
use Dotenv\Dotenv;
use \App\Common\Cli\Cli;

Dotenv::createUnsafeImmutable(__DIR__ . '/../', '.env')->load();
define("APP_DEBUG", env('APP_DEBUG'));

Error::register();

Cli::init();

// 使用字符串动态加载类
$cmds = [
    [
        'name' => 'hello',
        'short' => 'hello',
        'class' => 'App\Command\Hello',
    ]
];
Cli::addArrCommand(...$cmds)->run();
