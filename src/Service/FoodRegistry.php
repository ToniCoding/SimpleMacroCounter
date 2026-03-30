<?php

namespace src\Service;

use src\DTO\FoodDTO;
use src\Entity\Food;
use src\Entity\User;
use src\Repository\FoodsRepository;

use function src\Helpers\calorieCalc;

class FoodRegistry {
    public function __construct(
        private FoodsRepository $foodsRepository,
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

    public function getFoodsByMarket(string $market = '', int $offset = 1): array {
        $queryResult = $this->foodsRepository->getFoodsByMarket($market, $offset);
        $formattedData = [];

        foreach ($queryResult as $food) {
            $foodData =  [ucfirst($food->getName()), ucfirst($food->getMarket()), calorieCalc($food), $food->getProtein(), $food->getCarbs(), $food->getFats(), $food->getFiber()];
            array_push($formattedData, $foodData);
        }

        return $formattedData;
    }
}
