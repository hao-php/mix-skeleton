<?php

namespace App\Container;

use App\Lib\App\Monolog\RequestIdProcessor;
use App\Once;
use Haoa\Util\Util;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Processor\IntrospectionProcessor;

/**
 * Class Logger
 * @package App\Container
 */
class Logger implements HandlerInterface
{

    /**
     * @var \Monolog\Logger
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
     * @return \Monolog\Logger
     */
    public static function instance(): \Monolog\Logger
    {
        if (!isset(self::$instance)) {
            try {
                static::$once->do(function () {
                    $logger = new \Monolog\Logger('MIX');
                    if (Util::isCoroutine()) {
                        $logger->useLoggingLoopDetection(false); // 协程专用
                    }
                    $level = APP_DEBUG ? Level::Debug : Level::Info;
                    $extension = config('logger.file_extension') ?? 'log';
                    $rotatingFileHandler = new RotatingFileHandler(__DIR__ . "/../../runtime/logs/mix." . $extension, 7, $level);
                    $rotatingFileHandler->setFormatter(config('logger.formatter'));
                    $rotatingFileHandler->pushProcessor(new IntrospectionProcessor());
//                    $rotatingFileHandler->pushProcessor(new RequestIdProcessor());
                    $logger->pushHandler($rotatingFileHandler);
                    $logger->pushHandler(new Logger());
                    self::$instance = $logger;
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
     * @param LogRecord $record
     * @return bool
     */
    public function isHandling(LogRecord $record): bool
    {
        if (APP_DEBUG) {
            return $record->level->toRFC5424Level() >= Level::Debug;
        }
        return $record->level->toRFC5424Level() >= Level::Info;
    }

    /**
     * @param LogRecord $record
     * @return bool
     */
    public function handle(LogRecord $record): bool
    {
        $message = sprintf("%s  %s  %s\n", $record->datetime->format('Y-m-d H:i:s.u'), $record->level->toPsrLogLevel(), $record->message);
        switch (PHP_SAPI) {
            case 'cli':
            case 'cli-server':
                file_put_contents("php://stdout", $message);
                break;
        }
        return false;
    }

    /**
     * @param array $records
     * @return void
     */
    public function handleBatch(array $records): void
    {
        // TODO: Implement handleBatch() method.
    }

    /**
     * @return void
     */
    public function close(): void
    {
        // TODO: Implement close() method.
    }

}

Logger::init();
