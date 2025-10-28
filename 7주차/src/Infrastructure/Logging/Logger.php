<?php

namespace Ginger\Infrastructure\Logging;

use Monolog\Logger as Monologger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class Logger
{
    private static array $instances = [];
    private static string $defaultChannel = 'Ginger';
    private static string $logPath = __DIR__ . '/../../../logs/Ginger.log';

    /**
     * Monolog Logger 인스턴스를 가져옵니다.
     */
    public static function getInstance(?string $channel = null): Monologger
    {
        $channel = $channel ?? self::$defaultChannel;

        if (!isset(self::$instances[$channel])) {
            $logger = new Monologger($channel);

            $formatter = new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s',
                true,
                true
            );

            $handler = new RotatingFileHandler(self::$logPath, 30, Monologger::DEBUG);
            $handler->setFormatter($formatter);

            $logger->pushHandler($handler);

            self::$instances[$channel] = $logger;
        }

        return self::$instances[$channel];
    }

    /**
     * 모든 로그 레벨 헬퍼 메서드를 동적으로 처리합니다.
     */
    public static function __callStatic(string $method, array $arguments): void
    {
        $logger = self::getInstance();

        if (method_exists($logger, $method)) {
            $logger->$method(...$arguments);
        } else {
            throw new \BadMethodCallException("Undefined log level: {$method}");
        }
    }
}
