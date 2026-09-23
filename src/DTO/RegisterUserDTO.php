<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO {
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $username = '',

        #[Assert\NotBlank]
        public readonly string $password = '',

        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email = '',

        #[Assert\NotBlank]
        public readonly string $alias = '',

        #[Assert\NotBlank]
        #[Assert\GreaterThanOrEqual(15)]
        #[Assert\LessThanOrEqual(100)]
        public readonly int $age = 0
    ) {}
    
    public function toString(): string {
        return 'Username: ' . $this->username .
        "\n\tPassword: " . $this->password .
        "\n\tEmail: " . $this->email .
        "\n\tAlias: " . $this->alias .
        "\n\tAge: " . $this->age;
    }
}
