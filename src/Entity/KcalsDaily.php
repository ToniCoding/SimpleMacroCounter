<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "kcals_daily")]
class KcalsDaily {
    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "kcalsDailyRecords")]
        #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
        private User $user,
        
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: "integer")]
        private ?int $id = null,

        #[ORM\Column(type: "datetime_immutable")]
        private \DateTimeImmutable $date = new \DateTimeImmutable(),

        #[ORM\Column(type: "integer")]
        private int $kcals = 0,

        #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
        private string $protein = '0.00',

        #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
        private string $carbs = '0.00',

        #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
        private string $fats = '0.00',

        #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
        private string $fiber = '0.00'
    ) {
        $this->user = $user;
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

    public function getDate(): \DateTimeImmutable {
        return $this->date;
    }

    public function getKcals(): int {
        return $this->kcals;
    }

    public function setKcals(int $kcals): void {
        $this->kcals = $kcals;
    }

    public function getProtein(): string {
        return $this->protein;
    }

    public function setProtein(string $protein): void {
        $this->protein = $protein;
    }

    public function getCarbs(): string {
        return $this->carbs;
    }

    public function setCarbs(string $carbs): void {
        $this->carbs = $carbs;
    }

    public function getFats(): string {
        return $this->fats;
    }

    public function setFats(string $fats): void {
        $this->fats = $fats;
    }

    public function getFiber(): string {
        return $this->fiber;
    }

    public function setFiber(string $fiber): void {
        $this->fiber = $fiber;
    }
}
