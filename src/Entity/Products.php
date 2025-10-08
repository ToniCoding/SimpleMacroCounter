<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "products")]
class Products {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 75)]
    private string $productName;

    #[ORM\Column(type: "string", length: 50)]
    private string $market;

    #[ORM\Column(type: "smallint")]
    private int $kcal;

    #[ORM\Column(type: "decimal", precision: 5, scale: 2)]
    private string $protein;

    #[ORM\Column(type: "decimal", precision: 5, scale: 2)]
    private string $carbs;

    #[ORM\Column(type: "decimal", precision: 5, scale: 2)]
    private string $fats;

    #[ORM\Column(type: "decimal", precision: 5, scale: 2)]
    private string $fiber;


    public function getId(): ?int {
        return $this->id;
    }

    public function getProductName(): string {
        return $this->productName;
    }

    public function setProductName(string $productName): void {
        $this->productName = $productName;
    }

    public function getMarket(): string {
        return $this->market;
    }

    public function setMarket(string $market): void {
        $this->market = $market;
    }

    public function getKcal(): int {
        return $this->kcal;
    }

    public function setKcal(int $kcal): void {
        $this->kcal = $kcal;
    }

    public function getProtein(): string {
        return $this->protein;
    }

    public function setProtein(float $protein): void {
        $this->protein = $protein;
    }

    public function getCarbs(): string {
        return $this->carbs;
    }

    public function setCarbs(float $carbs): void {
        $this->carbs = $carbs;
    }

    public function getFats(): string {
        return $this->fats;
    }

    public function setFats(float $fats): void {
        $this->fats = $fats;
    }
    
    public function getFiber(): string {
        return $this->fiber;
    }

    public function setFiber(float $fiber): void {
        $this->fiber = $fiber;
    }
}
