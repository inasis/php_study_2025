<?php
declare(strict_types=1);

namespace Ginger\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property string $email
 * @property string $name
 * @property string $password
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
class User extends Model
{
    protected $primaryKey = 'email';
    protected $table = 'users';
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

    /**
     * 주어진 비밀번호가 저장된 해시와 일치하는지 확인합니다.
     *
     * @param string $plainPassword 사용자가 입력한 평문 비밀번호
     * @return bool 일치하면 true, 아니면 false
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}