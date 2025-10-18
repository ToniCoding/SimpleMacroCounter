<?php

namespace src\Entity;

use Doctrine\ORM\Mapping as ORM;

use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'access_token')]
class AccessToken {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $value;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $expiresAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function getId(): ?int {
        return $this->id;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function setValue(string $value): void {
        $this->value = $value;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }

    public function getExpiresAt(): DateTimeImmutable {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeImmutable $expiresAt): void {
        $this->expiresAt = $expiresAt;
    }

    public function isValid(): bool {
        return $this->expiresAt > new DateTimeImmutable();
    }

    public function getUserIdentifier(): string {
        return $this->user->getUsername();
    }
}