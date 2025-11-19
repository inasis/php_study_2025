<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Persistence\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $password
 * @property \DateTimeInterface $created_at
 * @property \DateTimeInterface $updated_at
 */
class User extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'users';
    protected $keyType = 'int';
    
    protected $fillable = [
        'email',
        'name',
        'password',
        'last_login_at',
    ];
    protected $hidden = [
        'password',
    ];

    public $incrementing = true;
    public $timestamps = true;

    /**
     * password 속성이 설정될 때 자동으로 해싱합니다.
     *
     * @return Attribute
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]),
        );
    }
}