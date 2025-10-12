<?php

namespace App\Entity;

use DateTime, DateTimeImmutable;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(
    name: "users",
    uniqueConstraints: [
        new UniqueConstraint(name: "uniq_username", columns: ["username"]),
        new UniqueConstraint(name: "uniq_email", columns: ["email"])
    ]
)]
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

    #[ORM\Column(type: "string", length: 10)]
    private string $status;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $createdTime;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $lastLogin;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: KcalsDaily::class, cascade: ["persist", "remove"])]
    private Collection $kcalsDailyRecords;

    public function __construct(DateTimeImmutable $createdTime, string $status) {
        $this->createdTime = $createdTime;
        $this->status = $status;
        $this->kcalsDailyRecords = new ArrayCollection();
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

    public function getStatus(): string {
        return $this->status;
    }

    public function setStatus(string $status): void {
        $this->status = $status;
    }

    public function getCreatedTime(): DateTimeImmutable {
        return $this->createdTime;
    }

    public function setCreatedTime(DateTimeImmutable $createdTime): void {
        $this->createdTime = $createdTime;
    }

    public function getLastLogin(): ?DateTime {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTime $lastLogin): void {
        $this->lastLogin = $lastLogin;
    }

    public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
    }

    public function getKcalsDailyRecords(): Collection {
        return $this->kcalsDailyRecords;
    }

    public function addKcalsDailyRecord(KcalsDaily $record): void {
        if (!$this->kcalsDailyRecords->contains($record)) {
            $this->kcalsDailyRecords->add($record);
            $record->setUser($this);
        }
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
            $this->status
        );
    }
}
