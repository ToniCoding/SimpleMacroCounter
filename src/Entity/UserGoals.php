<?php

namespace App\Entity;

use DateTime;

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
    private int $protein;

    #[ORM\Column(type: "integer")]
    private int $carbs;

    #[ORM\Column(type: "integer")]
    private int $fats;

    #[ORM\Column(type: "datetime")]
    private DateTime $dateTime;

    public function __construct(User $user, DateTime $dateTime) {
        $this->user = $user;
        $this->dateTime = $dateTime ?? new DateTime();
    }

    public function getUser(): User {
        return $this->user;
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
