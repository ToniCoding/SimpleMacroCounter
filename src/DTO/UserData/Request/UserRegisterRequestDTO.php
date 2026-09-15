<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class UserRegisterRequestDTO {
    public function __construct(
        #[Assert\NotBlank]
        private string $username = '',

        #[Assert\NotBlank]
        private string $password = '',

        #[Assert\NotBlank]
        #[Assert\Email]
        private string $email = '',

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
        "\n\tAge: " . $this->getAge();
    }
}
