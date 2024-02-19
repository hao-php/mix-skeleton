<?php

namespace App\Container;

use App\Common\Const\RunContextConst;
use App\Once;
use Haoa\Util\Context\RunContext;
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
//                    $rotatingFileHandler->pushProcessor(new IntrospectionProcessor());
                    $rotatingFileHandler->pushProcessor(function (LogRecord $record) {
                        $traceId = RunContext::get(RunContextConst::LOG_TRACE_ID);
                        if (!empty($traceId)) {
                            $record->extra['trace_id'] = $traceId;
                        }
                        $record->extra['cid'] = \Swoole\Coroutine::getCid();

                        /** @var Level $backtraceLevel */
                        $backtraceLevel = config('logger.backtrace_level');
                        if ($backtraceLevel && $record->level->value >= $backtraceLevel->value) {
                            $debug = debug_backtrace(2);
                            if (!empty($debug)) {
                                $backtrace = [];
                                foreach ($debug as $v) {
                                    if (!isset($v['file'])) {
                                        continue;
                                    }
                                    if (str_contains($v['file'], 'Common/Log/CateLog.php')) {
                                        continue;
                                    }
                                    if (str_contains($v['file'], '/vendor/')) {
                                        continue;
                                    }
                                    $backtrace[] = $v['file'] . ':' . $v['line'];
                                    break;
                                }
                                $record->extra['backtrace'] = $backtrace;
                            }
                        }

                        return $record;
                    });
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
            return $record->level->value >= Level::Debug->value;
        }
        return $record->level->value >= Level::Info->value;
    }

    /**
     * @param LogRecord $record
     * @return bool
     */
    public function handle(LogRecord $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }
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
