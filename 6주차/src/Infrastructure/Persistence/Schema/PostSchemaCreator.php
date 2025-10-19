<?php

namespace Fondue\Infrastructure\Persistence\Schema;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * 'posts' 테이블의 생성 및 구조 관리를 담당합니다.
 */
class PostSchemaCreator
{
    /**
     * 'posts' 테이블을 생성합니다.
     */
    public static function createTableIfNotExists(): void
    {
        $schema = Capsule::schema();

        if (!$schema->hasTable('posts')) {
            $schema->create('posts', function ($table) {
                $table->increments('id');
                $table->string('title', 255);
                $table->text('content');
                $table->timestamps(); // created_at 및 updated_at 컬럼 추가 필요
            });
        }
    }
}