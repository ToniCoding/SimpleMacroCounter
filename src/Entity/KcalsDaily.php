<?php

namespace src\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "kcals_daily")]
class KcalsDaily {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "kcalsDailyRecords")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private User $user;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $date;

    #[ORM\Column(type: "integer")]
    private int $kcals;

    #[ORM\Column(type: "integer")]
    private int $protein;

    #[ORM\Column(type: "integer")]
    private int $carbs;

    #[ORM\Column(type: "integer")]
    private int $fats;

    public function __construct(User $user) {
        $this->user = $user;
        $this->date = new DateTimeImmutable();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }

    public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    public function getKcals(): int {
        return $this->kcals;
    }

    public function setKcals(int $kcals): void {
        $this->kcals = $kcals;
    }

    public function getProtein(): int {
        return $this->protein;
    }

    public function setProtein(int $protein): void {
        $this->protein = $protein;
    }

    public function getCarbs(): int {
        return $this->carbs;
    }

    public function setCarbs(int $carbs): void {
        $this->carbs = $carbs;
    }

    public function getFats(): int {
        return $this->fats;
    }

    public function setFats(int $fats): void {
        $this->fats = $fats;
    }
}
