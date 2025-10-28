<?php

namespace Ginger\Infrastructure\Persistence\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class UserSchemaCreator
{
    public static function createTable(): void
    {
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function (Blueprint $table) {
                $table->string('email', 255)->primary();
                $table->string('password');
                $table->string('name')->nullable();
                $table->timestamps();
            });
        }
    }

    public static function dropTable(): void
    {
        Capsule::schema()->dropIfExists('users');
    }
}