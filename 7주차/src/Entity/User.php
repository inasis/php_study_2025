<?php
declare(strict_types=1);

namespace Ginger\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $email
 * @property string $name
 * @property string $password_hash
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'email';
    public $incrementing = false;

    // 기본 키의 데이터 타입은 문자열입니다
    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'name',
        'password',
    ];
    protected $hidden = [
        'password',
    ];

    public $timestamps = true;
}