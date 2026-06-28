<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO {
    public function __construct(
        #[Assert\NotBlank]
        private string $username = '',

        #[Assert\NotBlank]
        private string $password = '',

        #[Assert\NotBlank]
        #[Assert\Email]
        private string $email = '',

        #[Assert\NotBlank]
        private string $alias = '',

        #[Assert\NotBlank]
        #[Assert\GreaterThanOrEqual(15)]
        #[Assert\LessThanOrEqual(100)]
        private int $age = 0
    ) {}
    
    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $newUsername): void {
        $this->username = $newUsername;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $newPassword): void {
        $this->password = $newPassword;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $newEmail): void {
        $this->email = $newEmail;
    }

    public function getAlias(): string {
        return $this->alias;
    }

    public function setAlias(string $newAlias): void {
        $this->alias = $newAlias;
    }

    public function getAge(): int {
        return $this->age;
    }

    public function setAge(int $newAge): void {
        $this->age = $newAge;
    }

    public function toString(): string {
        return 'Username: ' . $this->getUsername() .
        "\n\tPassword: " . $this->getPassword() .
        "\n\tEmail: " . $this->getEmail() .
        "\n\tAlias: " . $this->getAlias() .
        "\n\tAge: " . $this->getAge();
    }
}
