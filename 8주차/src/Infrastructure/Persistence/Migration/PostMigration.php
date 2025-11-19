<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Persistence\Migration;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * 'posts' 테이블의 생성 및 구조 관리를 담당합니다.
 */
class PostMigration
{
    /**
     * 'posts' 테이블을 생성합니다.
     */
    public static function createTable(): void
    {
        $schema = Capsule::schema();

        if (!$schema->hasTable('posts')) {
            $schema->create('posts', function ($table) {
                $table->increments('id');
                $table->string('title', 255);
                $table->text('content');
                $table->integer('author_id');
                $table->timestamps();
            });
        }
    }
}