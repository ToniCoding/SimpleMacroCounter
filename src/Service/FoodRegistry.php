<?php

namespace src\Service;

use src\DTO\FoodDTO;
use src\DTO\MacroDataDTO;
use src\Entity\{User, Food};
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

    public function getFoodsByMarket(int $offset = 1, string $market = ''): array {
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
                $this->kcalsDailyRepository->updateMacroIntake($user, $this->foodToMacroDTO($foundFood));
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    private function foodToMacroDTO(Food $food) {
        $macroDTO = new MacroDataDTO();

        $macroDTO->setProtein($food->getProtein());
        $macroDTO->setCarbs($food->getCarbs());
        $macroDTO->setFats($food->getFats());
        $macroDTO->setFiber($food->getFiber());
        $macroDTO->setCalories(calorieCalc($food));
        
        return $macroDTO;
    }
}
