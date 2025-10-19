<?php

namespace Fondue\Infrastructure\Logging;

use Monolog\Logger as Monologger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class Logger
{
    private static $instances = [];
    
    /**
     * Logger 인스턴스 가져오기
     */
    public static function getInstance(string $channel = 'Fondue'): Monologger
    {
        if (!isset(self::$instances[$channel])) {
            self::$instances[$channel] = self::createLogger($channel);
        }
        
        return self::$instances[$channel];
    }
    
    /**
     * Logger 생성
     */
    private static function createLogger(string $channel): Monologger
    {
        $logger = new Monologger($channel);
        
        // 로그 포맷 설정
        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',
            true,
            true
        );
        
        // 단일 로그 핸들러
        $singleHandler = new RotatingFileHandler(
            __DIR__ . '/../../../logs/Fondue.log',
            30, // 30일 보관
            Monologger::DEBUG // DEBUG 레벨부터 기록하도록 설정
        );
        $singleHandler->setFormatter($formatter);
        $logger->pushHandler($singleHandler);
        
        return $logger;
    }
    
    /**
     * RFC 5424 표준 헬퍼 메서드
     */
     
    // Level 800: 시스템을 사용할 수 없는 상태
    public static function emergency($message, array $context = [])
    {
        self::getInstance()->emergency($message, $context);
    }
    
    // Level 700: 즉각적인 조치가 필요한 상태
    public static function alert($message, array $context = [])
    {
        self::getInstance()->alert($message, $context);
    }
    
    // Level 600: 치명적인 오류
    public static function critical($message, array $context = [])
    {
        self::getInstance()->critical($message, $context);
    }
    
    // Level 500: 일반적인 실행 오류
    public static function error($message, array $context = [])
    {
        self::getInstance()->error($message, $context);
    }
    
    // Level 400: 잠재적인 문제 가능성
    public static function warning($message, array $context = [])
    {
        self::getInstance()->warning($message, $context);
    }
    
    // Level 300: 정상 작동 중이지만 주목할 만한 이벤트 발생
    public static function notice($message, array $context = [])
    {
        self::getInstance()->notice($message, $context);
    }
    
   // Level 200: 성공적인 요청 등 일반적인 흥미로운 이벤트
    public static function info($message, array $context = [])
    {
        self::getInstance()->info($message, $context);
    }
    
    // Level 100: 상세 디버깅 정보
    public static function debug($message, array $context = [])
    {
        self::getInstance()->debug($message, $context);
    }
}