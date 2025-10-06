<?php

namespace App\Entity;

use DateTime, DateTimeImmutable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $username;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "string", length: 255)]
    private string $userAlias;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "integer")]
    private int $age;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $createdTime;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $lastLogin;

    #[ORM\Column(type: "boolean")]
    private bool $isActive;

    public function __construct(DateTimeImmutable $createdTime, bool $isActive) {
        $this->createdTime = $createdTime;
        $this->isActive = $isActive;
    }

      public function getId(): ?int {
        return $this->id;
    }

     public function getUsername(): string {
        return $this->username;
    }

     public function setUsername(string $username): void {
        $this->username = $username;
    }

     public function getPassword(): string {
        return $this->password;
    }

     public function setPassword(string $password): void {
        $this->password = $password;
    }

     public function getUserAlias(): string {
        return $this->userAlias;
    }

     public function setUserAlias(string $userAlias): void {
        $this->userAlias = $userAlias;
    }

     public function getEmail(): string {
        return $this->email;
    }

     public function setEmail(string $email): void {
        $this->email = $email;
    }

     public function getAge(): int {
        return $this->age;
    }

     public function setAge(int $age): void {
        $this->age = $age;
    }

     public function getCreatedTime(): DateTimeImmutable {
        return $this->createdTime;
    }

     public function setCreatedTime(DateTimeImmutable $createdTime): void {
        $this->createdTime = $createdTime;
    }

     public function getLastLogin(): DateTime {
        return $this->lastLogin;
    }

     public function setLastLogin(?DateTime $lastLogin): void {
        $this->lastLogin = $lastLogin;
    }

     public function getIsActive(): bool {
        return $this->isActive;
    }

     public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
    }

    public function __toString(): string {
        return sprintf(
            'User[id=%s, username=%s, alias=%s, email=%s, age=%d, created=%s, lastLogin=%s, active=%s]',
            $this->id ?? 'null',
            $this->username,
            $this->userAlias,
            $this->email,
            $this->age,
            $this->createdTime,
            $this->lastLogin,
            $this->isActive ? 'true' : 'false'
        );
    }
}
