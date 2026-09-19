<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use App\DTO\{MacroDataDTO, ProductsDTO};
use App\Entity\{User, Food, Products};
use App\Repository\{FoodsRepository, KcalsDailyRepository, ProductsRepository};
use App\Helpers\CalorieCalculator;

/**
 * Service responsible for managing product registrations, food searches across markets,
 * product catalogs, and food intake registrations mapped to daily macronutrient goals.
 */
class FoodRegistry {
    
    /**
     * Initializes the service with required repositories, calorie calculator, and logger.
     * 
     * @param FoodsRepository $foodsRepository Repository for managing legacy food entities.
     * @param ProductsRepository $productsRepository Repository for managing product catalog entities.
     * @param KcalsDailyRepository $kcalsDailyRepository Repository for updating daily macro intake.
     * @param CalorieCalculator $calorieCalculator Helper service to compute total calories.
     * @param LoggerInterface $logger Logger service for recording service actions and errors.
     */
    public function __construct(
        private FoodsRepository $foodsRepository,
        private ProductsRepository $productsRepository,
        private KcalsDailyRepository $kcalsDailyRepository,
        private CalorieCalculator $calorieCalculator,
        private LoggerInterface $logger
    ) {}

    /**
     * Registers a new product in the database from a product transfer object.
     * 
     * @param ProductsDTO $productDTO The DTO containing the product details to register.
     * @param User $user The user registering the product.
     * @return void
     */
    public function createFood(ProductsDTO $productDTO, User $user): void {
        $this->logger->info('[FOOD_REGISTRY_SERVICE] Registering new Food.');
        
        $product = new Products($user);

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

    /**
     * Retrieves a paginated list of products filtered by market, with optional formatting.
     * 
     * @param int $page The current page number for pagination.
     * @param string $market Optional market filter string.
     * @param string $format The formatting style for product fields (e.g., 'human').
     * @param int $limit The maximum number of items per page.
     * @return array Returns an array containing formatted product data and pagination metadata.
     */
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

    /**
     * Searches for products matching an autocomplete query string.
     * 
     * @param string $query The search query string.
     * @return array Returns a formatted array of matching product summaries.
     */
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

    /**
     * Performs a full-text search on products with pagination support.
     * 
     * @param string $query The full-text search query string.
     * @param int $page The current page number.
     * @param int $limit The maximum number of items per page.
     * @return array Returns an array containing search results and pagination details.
     */
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

    /**
     * Registers a food intake entry for a user based on consumed grams and product ID.
     * 
     * @param array $intake The intake submission data containing product ID and grams.
     * @param User $user The user recording the food intake.
     * @return bool Returns true on successful registration, false otherwise.
     */
    public function registerFoodIntake(array $intake, User $user): bool {
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

    /**
     * Converts a food or product entity and consumed grams into a macro data DTO.
     * 
     * @param Food|Products $food The food or product entity being consumed.
     * @param float $gramsConsumed The amount consumed in grams.
     * @return MacroDataDTO Returns the calculated macro data DTO.
     */
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
