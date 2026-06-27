<?php

namespace src\Service;

use Psr\Log\LoggerInterface;
use src\DTO\{MacroDataDTO, ProductsDTO};
use src\Entity\{User, Food, Products};
use src\Repository\{FoodsRepository, KcalsDailyRepository, ProductsRepository};
use src\Helpers\CalorieCalculator;

class FoodRegistry {
    public function __construct(
        private FoodsRepository $foodsRepository,
        private ProductsRepository $productsRepository,
        private KcalsDailyRepository $kcalsDailyRepository,
        private CalorieCalculator $calorieCalculator,
        private LoggerInterface $logger
    ) {}

    public function createFood(ProductsDTO $productDTO, User $user): void {
        $this->logger->info('[FOOD_REGISTRY_SERVICE] Registering new Food.');
        
        $product = new Products();

        $product->setProductName($productDTO->getProductName());
        $product->setBrand($productDTO->getBrand());
        $product->setMarket($productDTO->getMarket());
        $product->setProtein((float) $productDTO->getProtein());
        $product->setCarbs((float) $productDTO->getCarbs());
        $product->setFats((float) $productDTO->getFats());
        $product->setFiber((float) $productDTO->getFiber());
        $product->setKcal($this->calorieCalculator->calorieCalc($product));
        

        $this->logger->info('[FOOD_REGISTRY_SERVICE] Registering new product.');
        $this->logger->notice('[FOOD_REGISTRY_SERVICE] Product to registry: ' . $product->__toString());

        $this->productsRepository->registerProduct($product);
    }

    public function getProductsByMarket(int $page = 1, string $market = '', string $format = 'human', int $limit = 100): array {
        $offset = ($page - 1) * $limit;
        $result = $this->productsRepository->getProductsByMarket($market, $offset, $limit);

        $formattedData = [];
        foreach ($result['data'] as $product) {
            $foodData = [
                $product->getProductName(),
                $product->getMarket(),
                $product->getKcal(),
                $product->getProtein(),
                $product->getCarbs(),
                $product->getFats(),
                $product->getFiber(),
                $product->getId(),
                $product->getBrand()
            ];

            if ($format === 'human') {
                $foodData[0] = ucfirst($foodData[0]);
                $foodData[1] = ucfirst($foodData[1]);
            }

            $formattedData[] = $foodData;
        }

        return [
            'data' => $formattedData,
            'pagination' => [
                'currentPage' => $result['currentPage'],
                'totalPages' => $result['totalPages'],
                'totalItems' => $result['total'],
                'itemsPerPage' => $limit,
                'hasNext' => $result['currentPage'] < $result['totalPages'],
                'hasPrevious' => $result['currentPage'] > 1
            ]
        ];
    }

    public function searchProducts(string $query): array {
        $results = $this->productsRepository->autocomplete($query, 10);

        $formatted = [];
        foreach ($results as $product) {
            $formatted[] = [
                'id' => $product->getId(),
                'name' => $product->getProductName(),
                'market' => $product->getMarket(),
                'kcal' => $product->getKcal()
            ];
        }
        return $formatted;
    }

    public function searchProductsByFullText(string $query, int $page = 1, int $limit = 125): array {
        $offset = ($page - 1) * $limit;
        $results = $this->productsRepository->fullTextSearch($query, $offset, $limit);

        $formatted = [];
        foreach ($results['data'] as $product) {
            $formatted[] = [
                'id' => $product->getId(),
                'name' => $product->getProductName(),
                'market' => $product->getMarket(),
                'kcal' => $product->getKcal(),
                'protein' => $product->getProtein(),
                'carbs' => $product->getCarbs(),
                'fats' => $product->getFats(),
                'fiber' => $product->getFiber(),
                'brand' => $product->getBrand()
            ];
        }

        return [
            'data' => $formatted,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $results['totalPages'],
                'totalItems' => $results['total'],
                'itemsPerPage' => $limit,
                'hasNext' => $page < $results['totalPages'],
                'hasPrevious' => $page > 1
            ]
        ];
    }

    // This code cannot be used as long as the database hotfix for the mixin of Products and Foods is applied.
    // public function getFoodsByMarket(int $offset = 1, string $market = '', string $format = 'human'): array {
    //     $queryResult = $this->foodsRepository->getFoodsByMarket($market, $offset);
    //     $formattedData = [];

    //     foreach ($queryResult as $food) {
    //         $foodData = [
    //             $food->getName(),
    //             $food->getMarket(),
    //             calorieCalc($food),
    //             $food->getProtein(),
    //             $food->getCarbs(),
    //             $food->getFats(),
    //             $food->getFiber(),
    //             $food->getId()
    //         ];

    //         if ($format === 'human') {
    //             $foodData[0] = ucfirst($foodData[0]);
    //             $foodData[1] = ucfirst($foodData[1]);
    //         }

    //         $formattedData[] = $foodData;
    //     }

    //     return $formattedData;
    // }

    public function registerFoodIntake(array $intake, User $user): bool {
        //$foundFood = $this->foodsRepository->getFood((int) $intake['id']);
        $foundFood = $this->productsRepository->findOneBy(['id' => (int) $intake['id']]);
        $gramsConsumed = (float) ($intake['grams'] ?? 0);

        if (!$foundFood) {
            $this->logger->warning('[FOOD_REGISTRY_SERVICE] Food not found while registering an intake related to it.');
            return false;
        }

        try {
            $this->kcalsDailyRepository->updateMacroIntake(
                $user,
                $this->foodToMacroDTO($foundFood, $gramsConsumed)
            );

            $this->logger->info('[FOOD_REGISTRY_SERVICE] Successfully registered food with ID: ' . $foundFood->getProductName());
        } catch (\Throwable $e) {
            $this->logger->error('[FOOD_REGISTRY_SERVICE] Something went wrong while registering the intake for the food with ID: ' . $foundFood->getProductName());
            $this->logger->error('[FOOD_REGISTRY_SERVICE] Exception is: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    private function foodToMacroDTO(Food|Products $food, float $gramsConsumed): MacroDataDTO {
        $consumedMultiplier = $gramsConsumed / 100;

        $macroDTO = new MacroDataDTO();

        $macroDTO->setProtein((float) $food->getProtein() * $consumedMultiplier);
        $macroDTO->setCarbs((float) $food->getCarbs() * $consumedMultiplier);
        $macroDTO->setFats((float) $food->getFats() * $consumedMultiplier);
        $macroDTO->setFiber((float) $food->getFiber() * $consumedMultiplier);
        $macroDTO->setCalories((float) $this->calorieCalculator->calorieCalc($food) * $consumedMultiplier);

        return $macroDTO;
    }
}
