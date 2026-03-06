<?php

namespace src\Service;

use src\DTO\FoodDTO;
use src\Entity\Food;
use src\Entity\User;
use src\Repository\FoodsRepository;

class FoodRegistry {
    public function __construct(
        private FoodsRepository $foodsRepository
    ) {}

    public function createFood(FoodDTO $foodDTO, User $user): void {
        $food = new Food;
        $food->setName($foodDTO->getName());
        $food->setMarket($foodDTO->getMarket());
        $food->setProtein($foodDTO->getProtein());
        $food->setCarbs($foodDTO->getCarbs());
        $food->setFats($foodDTO->getFats());
        $food->setFiber($foodDTO->getFiber());
        $food->setUser($user);

        $this->foodsRepository->registerFood($food);
    }
}
