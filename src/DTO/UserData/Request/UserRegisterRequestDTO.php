<?php

namespace App\DTO\UserData\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterRequestDTO {
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $username = '',

        #[Assert\NotBlank]
        public readonly string $password = '',

        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email = '',

        #[Assert\NotBlank]
        #[Assert\GreaterThanOrEqual(15)]
        #[Assert\LessThanOrEqual(100)]
        public readonly int $age = 0
    ) {}
    
    public function toString(): string {
        return 'Username: ' . $this->username .
        "\n\tPassword: " . $this->password .
        "\n\tEmail: " . $this->email .
        "\n\tAge: " . $this->age;
    }
}
