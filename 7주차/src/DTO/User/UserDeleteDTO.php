<?php

namespace Ginger\DTO\User;

use Ginger\DTO\Validation\Attribute\Email;
use Ginger\DTO\Validation\Attribute\NotNull;

class UserDeleteDTO
{
    public function __construct(
        #[NotNull, Email]
        public string $email,

        #[NotNull]
        public string $password
    ) {}
}