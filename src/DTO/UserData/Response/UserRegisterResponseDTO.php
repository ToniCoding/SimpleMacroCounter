<?php

namespace App\DTO\UserData\Response;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterResponseDTO {
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $message = '',

        #[Assert\NotBlank]
        public readonly string $redirect_url = ''
    ) {}
    
    public function toString(): string {
        return 'Message: ' . $this->message .
        "\nRedirect URL: " . $this->redirect_url;
    }
}
