<?php

namespace src\Service;

use src\DTO\FoodDTO;
use src\Entity\{User, Food, KcalsDaily};
use src\Repository\FoodsRepository;

use src\Repository\KcalsDailyRepository;
use function src\Helpers\calorieCalc;

class FoodRegistry {
    public function __construct(
        private FoodsRepository $foodsRepository,
        private KcalsDailyRepository $kcalsDailyRepository,
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

    public function registerFoodIntake(array $intake, User $user) {
        $foundFood = $this->foodsRepository->getFood($intake['id']);

        if ($foundFood) {
            try {
                $this->kcalsDailyRepository->insertIntakeRegistry($this->foodToKcalsDaily($foundFood, $user));
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    private function foodToKcalsDaily(Food $food, User $user) {
        $kcalEntry = new KcalsDaily($user);

        $kcalEntry->setProtein($food->getProtein());
        $kcalEntry->setCarbs($food->getCarbs());
        $kcalEntry->setFats($food->getFats());
        $kcalEntry->setFiber($food->getFiber());
        $kcalEntry->setKcals(calorieCalc($food));

        return $kcalEntry;
    }
}
