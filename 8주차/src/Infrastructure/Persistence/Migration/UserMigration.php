<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Persistence\Migration;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class UserMigration
{
    public static function createTable(): void
    {
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('password');
                $table->string('name')->nullable();
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
                $table->timestamps();

                $table->index('email');
            });
        }
    }

    public static function dropTable(): void
    {
        Capsule::schema()->dropIfExists('users');
    }
}