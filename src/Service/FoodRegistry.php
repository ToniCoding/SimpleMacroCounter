<?php

namespace src\Service;

use Psr\Log\LoggerAwareInterface;
use src\DTO\FoodDTO;
use src\DTO\MacroDataDTO;
use src\Entity\{User, Food};
use src\Repository\FoodsRepository;
use Psr\Log\LoggerInterface;

use src\Repository\KcalsDailyRepository;
use function src\Helpers\calorieCalc;

class FoodRegistry
{
    public function __construct(
        private FoodsRepository $foodsRepository,
        private KcalsDailyRepository $kcalsDailyRepository,
    ) {
    }

    public function createFood(FoodDTO $foodDTO, User $user): void
    {
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

    public function getFoodsByMarket(int $offset = 1, string $market = '', string $format = 'human'): array
    {
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

    public function registerFoodIntake(array $intake, User $user)
    {
        $foundFood = $this->foodsRepository->getFood((int)$intake['id']);
        $gramsConsumed = $intake['grams'];

        if ($foundFood) {
            try {
                $this->kcalsDailyRepository->updateMacroIntake($user, $this->foodToMacroDTO($foundFood, $gramsConsumed));
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

    private function foodToMacroDTO(Food $food, int $gramsConsumed)
    {
        $macroDTO = new MacroDataDTO();

        $consumedMultiplier = $gramsConsumed / 100;
        $macroDTO->setProtein($food->getProtein() * $consumedMultiplier);
        $macroDTO->setCarbs($food->getCarbs() * $consumedMultiplier);
        $macroDTO->setFats($food->getFats() * $consumedMultiplier);
        $macroDTO->setFiber($food->getFiber() * $consumedMultiplier);
        $macroDTO->setCalories(calorieCalc($food) * $consumedMultiplier);

        return $macroDTO;
    }
}
