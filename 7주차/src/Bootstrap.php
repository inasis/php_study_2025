<?php
declare(strict_types=1);

namespace Ginger;

use Dotenv\Dotenv;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Ginger\Infrastructure\Persistence\DatabaseConnector;
use Ginger\Infrastructure\Persistence\Schema\PostSchemaCreator;
use Ginger\Infrastructure\Persistence\Schema\UserSchemaCreator;

class Bootstrap
{
    public static function initialize(): ContainerInterface
    {
        self::loadEnvironment();
        DatabaseConnector::boot();
        PostSchemaCreator::createTableIfNotExists();
        UserSchemaCreator::createTable();
        return self::initializeDIContainer();
    }

    /**
     * 환경 변수를 로드하고 주요 상수를 정의합니다.
     */
    private static function loadEnvironment(): void
    {
        // 환경 파일 로드
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->safeLoad();

        // APP_ENV에 따른 환경 파일 로드
        $appEnv = $_ENV['APP_ENV'] ?? 'development';
        $envFilePath = __DIR__ . "/../../.env.{$appEnv}";
        if (file_exists($envFilePath)) {
            $dotenv = Dotenv::createMutable(__DIR__ . '/../..', ".env.{$appEnv}");
            $dotenv->load(); // 환경별 설정이 기본값을 덮어쓰도록 변경
        }

        // 전역 상수 정의
        if (!defined('APP_ENV')) {
            define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
            define('APP_LOG_LEVEL', $_ENV['LOG_LEVEL'] ?? 'info');
            define('APP_LOG_PATH', $_ENV['LOG_PATH'] ?? '/logs/app.log');
        }
    }

    /**
     * PHP-DI 컨테이너를 초기화하고 필요한 서비스를 등록합니다.
     */
    private static function initializeDIContainer(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions([ 
            \Ginger\Repository\UserRepositoryInterface::class => \DI\autowire(\Ginger\Infrastructure\Persistence\Repository\UserRepository::class),
            \Ginger\Repository\PostRepositoryInterface::class => \DI\autowire(\Ginger\Infrastructure\Persistence\Repository\PostRepository::class),
            \Ginger\Service\JwtServiceInterface::class => \DI\autowire(\Ginger\Infrastructure\Security\JwtService::class),
            \Ginger\Service\UserService::class => \DI\autowire(),
            \Ginger\Service\AuthenticationService::class => \DI\autowire(), 
            \Ginger\Controller\UserController::class => \DI\autowire(),
            \Ginger\Controller\PostController::class => \DI\autowire(),
            \Ginger\DTO\Validation\Validator::class => \DI\autowire(),
            \Ginger\Controller\Middleware\AuthMiddleware::class => \DI\autowire(),
        ]);

        return $containerBuilder->build();
    }
}