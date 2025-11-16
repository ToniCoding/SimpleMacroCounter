<?php

namespace src\Entity;

use src\Entity\User;

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

    #[ORM\Column(type: "integer")]
    private int $protein;

    #[ORM\Column(type: "integer")]
    private int $carbs;

    #[ORM\Column(type: "integer")]
    private int $fats;

    #[ORM\Column(type: "integer")]
    private int $fiber;

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

    public function getFiber(): int {
        return $this->fiber;
    }

    public function setFiber(int $fiber): void {
        $this->fiber = $fiber;
    }
}
