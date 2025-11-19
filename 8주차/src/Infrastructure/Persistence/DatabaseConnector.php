<?php

namespace Hazelnut\Infrastructure\Persistence;

use Illuminate\Database\Capsule\Manager as Capsule;
use RuntimeException;

/**
 * 데이터베이스 연결 설정 및 Eloquent ORM을 부팅하는 책임을 가집니다.
 */
class DatabaseConnector
{
    private const DB_PATH_RELATIVE = '/../../../database.sqlite';

    /**
     * Eloquent ORM을 초기화하고 DB 연결을 처리합니다.
     */
    public static function boot(): void
    {
        $dbPath = __DIR__ . self::DB_PATH_RELATIVE;
        self::ensureDatabaseFileExists($dbPath);

        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => $dbPath,
            'prefix' => '',
        ]);

        // Eloquent를 전역으로 설정하고 사용할 수 있게 합니다.
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    /**
     * SQLite 데이터베이스 파일이 존재하는지 확인하고 필요하면 생성 및 권한을 설정합니다.
     * 
     * @param string $dbPath 데이터베이스 파일의 전체 경로
     */
    private static function ensureDatabaseFileExists(string $dbPath): void
    {
        if (file_exists($dbPath)) {
            return;
        }

        if (!touch($dbPath)) {
            throw new RuntimeException("데이터베이스 파일 $dbPath 생성에 실패했습니다.");
        }

        $isProduction = defined('APP_ENV') && APP_ENV === 'production';
        $permissions = $isProduction ? 0660 : 0666;
        
        // 오류 보고를 막기 위해 @ 사용
        if (!@chmod($dbPath, $permissions)) { 
            error_log("경고: 데이터베이스 파일 $dbPath 권한 설정을 실패했습니다.");
        }
    }
}