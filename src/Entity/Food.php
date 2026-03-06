<?php

namespace src\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "foods")]
class Food {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'foods')]
    private ?User $user = null;

    #[ORM\Column(type: "string")]
    private string $name;

    #[ORM\Column(type: "integer")]
    private int $protein;

    #[ORM\Column(type: "integer")]
    private int $carbs;

    #[ORM\Column(type: "integer")]
    private int $fats;

    #[ORM\Column(type: "integer")]
    private int $fiber;

    #[ORM\Column(type: "string")]
    private string $market;

    public function getId(): int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): void {
        $this->user = $user;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
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

    public function getMarket(): string {
        return $this->market;
    }

    public function setMarket(string $market): void {
        $this->market = $market;
    }
}
