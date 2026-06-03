<?php

namespace src\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "foods")]
class Food
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'foods')]
    private ?User $user = null;

    #[ORM\Column(type: "string")]
    private string $name;

    #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
    private string $protein;

    #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
    private string $carbs;

    #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
    private string $fats;

    #[ORM\Column(type: "decimal", precision: 8, scale: 2)]
    private string $fiber;

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

    public function getMarket(): string {
        return $this->market;
    }

    public function setMarket(string $market): void {
        $this->market = $market;
    }

    public function __toString(): string {
        return 'Name: ' . $this->getName() .
        "\n\tProtein: " . $this->getProtein() .
        "\n\tCarbs: " . $this->getCarbs() .
        "\n\tFats: " . $this->getFats() .
        "\n\tFiber: " . $this->getFiber();
    }
}
