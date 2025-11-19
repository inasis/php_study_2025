<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Commence;

use Dotenv\Dotenv;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Hazelnut\Infrastructure\Persistence\DatabaseConnector;
use Hazelnut\Infrastructure\Persistence\Migration\PostMigration;
use Hazelnut\Infrastructure\Persistence\Migration\UserMigration;

class Bootstrap
{
    private static array $masterControllerRegistry = [];

    public static function initialize(): ContainerInterface
    {
        self::loadEnvironment();
        DatabaseConnector::boot();
        PostMigration::createTable();
        UserMigration::createTable();
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

        $projectRoot = dirname(__DIR__, 3);

        $containerBuilder->addDefinitions([ 
            \Hazelnut\Domain\Repository\UserRepositoryInterface::class => \DI\autowire(\Hazelnut\Infrastructure\Persistence\Repository\UserRepository::class),
            \Hazelnut\Domain\Repository\PostRepositoryInterface::class => \DI\autowire(\Hazelnut\Infrastructure\Persistence\Repository\PostRepository::class),

            \Hazelnut\Application\UseCase\Post\PublishPostUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Post\PublishPostService::class),
            \Hazelnut\Application\UseCase\Post\ViewPostUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Post\ViewPostService::class),
            \Hazelnut\Application\UseCase\Post\UpdatePostUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Post\UpdatePostService::class),
            \Hazelnut\Application\UseCase\Post\RemovePostUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Post\RemovePostService::class),

            \Hazelnut\Application\UseCase\User\RegisterUserUseCase::class => \DI\autowire(\Hazelnut\Application\Service\User\RegisterUserService::class),
            \Hazelnut\Application\UseCase\User\RetrieveUserUseCase::class => \DI\autowire(\Hazelnut\Application\Service\User\RetrieveUserService::class),
            \Hazelnut\Application\UseCase\User\ModifyUserUseCase::class => \DI\autowire(\Hazelnut\Application\Service\User\ModifyUserService::class),
            \Hazelnut\Application\UseCase\User\RemoveUserUseCase::class => \DI\autowire(\Hazelnut\Application\Service\User\RemoveUserService::class),

            \Hazelnut\Application\UseCase\Jwt\ExtractAccessTokenUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Jwt\ExtractAccessTokenService::class),
            \Hazelnut\Application\UseCase\Jwt\ExtractRefreshTokenUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Jwt\ExtractRefreshTokenService::class),
            \Hazelnut\Application\UseCase\Jwt\GenerateAccessTokenUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Jwt\GenerateAccessTokenService::class),
            \Hazelnut\Application\UseCase\Jwt\GenerateRefreshTokenUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Jwt\GenerateRefreshTokenService::class),
            \Hazelnut\Application\UseCase\Jwt\RenewAccessTokenUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Jwt\RenewAccessTokenService::class),

            \Hazelnut\Application\UseCase\Auth\LoginUseCase::class => \DI\autowire(\Hazelnut\Application\Service\Auth\LoginService::class),

            \Hazelnut\Application\Interface\JwtAdapterInterface::class => \DI\autowire(\Hazelnut\Infrastructure\Security\JwtAdapter::class),

            \Hazelnut\Presentation\Web\Controller\UserController::class => \DI\autowire(),
            \Hazelnut\Presentation\Web\Controller\PostController::class => \DI\autowire(),
            \Hazelnut\Application\DTO\Validation\Validator::class => \DI\autowire(),

            // 템플릿 Engine에 Factory를 사용하여 설정 경로 주입
            \ExpoOne\Engine::class => \DI\factory(function (ContainerInterface $c) use ($projectRoot) {
                return new \ExpoOne\Engine($projectRoot . '/storage/template', $projectRoot . '/storage/cache');
            }),
        ]);

        return $containerBuilder->build();
    }
}