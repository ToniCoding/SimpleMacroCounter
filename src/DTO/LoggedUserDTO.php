<?php

namespace src\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoggedUserDTO {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        private string $username = '',

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        private string $password = ''
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
}
