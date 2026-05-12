<?php

namespace src\Service;

use src\DTO\{FoodDTO, MacroDataDTO};
use src\Entity\{User, Food};
use src\Repository\{FoodsRepository, KcalsDailyRepository};
use function src\Helpers\calorieCalc;

class FoodRegistry {
    public function __construct(
        private FoodsRepository $foodsRepository,
        private KcalsDailyRepository $kcalsDailyRepository,
    ) {}

    public function createFood(FoodDTO $foodDTO, User $user): void {
        $food = new Food();
        $food->setName($foodDTO->getName());
        $food->setMarket($foodDTO->getMarket());
        $food->setProtein((float) $foodDTO->getProtein());
        $food->setCarbs((float) $foodDTO->getCarbs());
        $food->setFats((float) $foodDTO->getFats());
        $food->setFiber((float) $foodDTO->getFiber());
        $food->setUser($user);

        $this->foodsRepository->registerFood($food);
    }

    public function getFoodsByMarket(int $offset = 1, string $market = '', string $format = 'human'): array {
        $queryResult = $this->foodsRepository->getFoodsByMarket($market, $offset);
        $formattedData = [];

        foreach ($queryResult as $food) {
            $foodData = [
                $food->getName(),
                $food->getMarket(),
                calorieCalc($food),
                $food->getProtein(),
                $food->getCarbs(),
                $food->getFats(),
                $food->getFiber(),
                $food->getId()
            ];

            if ($format === 'human') {
                $foodData[0] = ucfirst($foodData[0]);
                $foodData[1] = ucfirst($foodData[1]);
            }

            $formattedData[] = $foodData;
        }

        return $formattedData;
    }

    public function registerFoodIntake(array $intake, User $user): bool {
        $foundFood = $this->foodsRepository->getFood((int) $intake['id']);
        $gramsConsumed = (float) ($intake['grams'] ?? 0);

        if (!$foundFood) {
            return false;
        }

        try {
            $this->kcalsDailyRepository->updateMacroIntake(
                $user,
                $this->foodToMacroDTO($foundFood, $gramsConsumed)
            );
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    private function foodToMacroDTO(Food $food, float $gramsConsumed): MacroDataDTO {
        $consumedMultiplier = $gramsConsumed / 100;

        $macroDTO = new MacroDataDTO();

        $macroDTO->setProtein((float) $food->getProtein() * $consumedMultiplier);
        $macroDTO->setCarbs((float) $food->getCarbs() * $consumedMultiplier);
        $macroDTO->setFats((float) $food->getFats() * $consumedMultiplier);
        $macroDTO->setFiber((float) $food->getFiber() * $consumedMultiplier);
        $macroDTO->setCalories((float) calorieCalc($food) * $consumedMultiplier);

        return $macroDTO;
    }
}
