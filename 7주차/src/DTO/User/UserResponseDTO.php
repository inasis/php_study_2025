<?php
declare(strict_types=1);

namespace Ginger\DTO\User;

use Ginger\Entity\UserInterface;

readonly class UserResponseDTO
{
    public string $email;
    public ?string $password;
    public string $name;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(UserInterface $user)
    {
        $this->email = $user->email;
        $this->password = $user->password;
        $this->name = $user->name;
        $this->createdAt = $user->created_at->format('Y-m-d H:i:s');
        $this->updatedAt = $user->updated_at->format('Y-m-d H:i:s');
    }
    
    /**
     * DTO의 public 속성을 포함하는 배열을 반환합니다.
     * 
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}