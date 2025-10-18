<?php

namespace src\Entity;

use DateTime, DateTimeImmutable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_metrics")]
class UserMetrics {
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private User $user;

    #[ORM\Column(type: "integer")]
    private int $creatine_streak;

    #[ORM\Column(type: "integer")]
    private int $experience;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function getCreatineStreak(): int {
        return $this->creatine_streak;
    }

    public function setCreatineStreak(int $addStreak): void {
        $this->creatine_streak = $this->getCreatineStreak() + $addStreak;
    }

    public function getExperience(): int {
        return $this->experience;
    }

    public function setExperience(int $experience): void {
        $this->experience = $this->getExperience() + $experience;
    }
}