<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoggedUserDTO {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $username = '',

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $password = ''
    ) {}

    public function toString(): string {
        return 'Username: ' . $this->username .
        "\n\tPassword: " . $this->password;
    }
}
