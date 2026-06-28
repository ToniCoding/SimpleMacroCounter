<?php

namespace App\Entity;

use App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_goals")]
class UserGoals {

    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private User $user;

    #[ORM\Column(type: "integer")]
    private int $calories;

    #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
    private string $protein;

    #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
    private string $carbs;

    #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
    private string $fats;

    #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
    private string $fiber;

    #[ORM\Column(type: "datetime")]
    private \DateTime $dateTime;

    public function __construct(User $user, \DateTime $dateTime) {
        $this->user = $user;
        $this->dateTime = $dateTime ?? new \DateTime();
    }

    public function getUser(): User {
        return $this->user;
    }

    public function getCalories(): int {
        return $this->calories;
    }

    public function setCalories(int $calories): void {
        $this->calories = $calories;
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
