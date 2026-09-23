<?php

namespace App\DTO\UserData\Response;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterResponseDTO {
    public function __construct(
        #[Assert\NotBlank]
        private string $message = '',

        #[Assert\NotBlank]
        private string $redirect_url = '',
    ) {}
    
    public function getMessage(): string {
        return $this->message;
    }

    public function setMessage(string $message): void {
        $this->message = $message;
    }

    public function getRedirectUrl(): string {
        return $this->redirect_url;
    }

    public function setRedirectUrl(string $redirect_url): void {
        $this->redirect_url = $redirect_url;
    }

    public function toString(): string {
        return 'Message: ' . $this->getMessage() .
        "\nRedirect URL: " . $this->getRedirectUrl();
    }
}
