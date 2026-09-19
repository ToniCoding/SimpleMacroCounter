<?php

namespace Smc\Tests\Service;

use App\DTO\MacroDataDTO;
use App\DTO\ProductsDTO;
use App\Entity\Products;
use App\Entity\User;
use App\Helpers\CalorieCalculator;
use App\Repository\FoodsRepository;
use App\Repository\KcalsDailyRepository;
use App\Repository\ProductsRepository;
use App\Service\FoodRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FoodRegistryTest extends TestCase {
    private $foodsRepository;
    private $productsRepository;
    private $kcalsDailyRepository;
    private $calorieCalculator;
    private $logger;
    private $user;
    private FoodRegistry $service;

    public function setUp(): void {
        $this->foodsRepository = $this->createMock(FoodsRepository::class);

        // Reflection para obtener todos los métodos públicos nativos y heredados de ProductsRepository
        $reflection = new \ReflectionClass(ProductsRepository::class);
        $publicMethods = array_filter(
            $reflection->getMethods(\ReflectionMethod::IS_PUBLIC),
            fn($m) => !$m->isConstructor() && !$m->isFinal() && !$m->isStatic()
        );
        $methodNames = array_values(array_map(fn($m) => $m->getName(), $publicMethods));

        $repoBuilder = $this->getMockBuilder(ProductsRepository::class)->disableOriginalConstructor();
        if (!empty($methodNames)) {
            $repoBuilder->onlyMethods($methodNames);
        }
        if (!method_exists(ProductsRepository::class, 'autocomplete')) {
            $repoBuilder->addMethods(['autocomplete']);
        }
        $this->productsRepository = $repoBuilder->getMock();

        $this->kcalsDailyRepository = $this->createMock(KcalsDailyRepository::class);
        $this->calorieCalculator = $this->createMock(CalorieCalculator::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->user = $this->createMock(User::class);

        $this->service = new FoodRegistry(
            $this->foodsRepository,
            $this->productsRepository,
            $this->kcalsDailyRepository,
            $this->calorieCalculator,
            $this->logger
        );
    }

    /**
     ********************************************************
     *** Tests for createFood method ************************
     ********************************************************
     */

    public function testCreateFoodSuccess(): void {
        $productDTO = $this->createMock(ProductsDTO::class);
        $productDTO->method('getProductName')->willReturn('Avena Integral');
        $productDTO->method('getBrand')->willReturn('Hacendado');
        $productDTO->method('getMarket')->willReturn('Mercadona');
        $productDTO->method('getProtein')->willReturn(13.5);
        $productDTO->method('getCarbs')->willReturn(58.0);
        $productDTO->method('getFats')->willReturn(7.0);
        $productDTO->method('getFiber')->willReturn(10.0);

        $this->calorieCalculator
            ->expects($this->once())
            ->method('calorieCalc')
            ->willReturn(357);

        $this->logger
            ->expects($this->exactly(2))
            ->method('info')
            ->with($this->stringContains('[FOOD_REGISTRY_SERVICE]'));

        $this->logger
            ->expects($this->once())
            ->method('notice')
            ->with($this->stringContains('[FOOD_REGISTRY_SERVICE] Product to registry:'));

        $this->productsRepository
            ->expects($this->once())
            ->method('registerProduct')
            ->with($this->callback(function (Products $product) {
                return $product->getProductName() === 'Avena Integral'
                    && $product->getBrand() === 'Hacendado'
                    && $product->getMarket() === 'Mercadona'
                    && $product->getProtein() === 13.5
                    && $product->getCarbs() === 58.0
                    && $product->getFats() === 7.0
                    && $product->getFiber() === 10.0
                    && $product->getKcal() === 357;
            }));

        $this->service->createFood($productDTO, $this->user);
    }

    public function testCreateFoodWithZeroAndDecimalValues(): void {
        $productDTO = $this->createMock(ProductsDTO::class);
        $productDTO->method('getProductName')->willReturn('Agua Mineral');
        $productDTO->method('getBrand')->willReturn('Bezoya');
        $productDTO->method('getMarket')->willReturn('Consum');
        $productDTO->method('getProtein')->willReturn(0.0);
        $productDTO->method('getCarbs')->willReturn(0.0);
        $productDTO->method('getFats')->willReturn(0.0);
        $productDTO->method('getFiber')->willReturn(0.0);

        $this->calorieCalculator
            ->expects($this->once())
            ->method('calorieCalc')
            ->willReturn(0);

        $this->productsRepository
            ->expects($this->once())
            ->method('registerProduct')
            ->with($this->callback(function (Products $product) {
                return $product->getProtein() === 0.0
                    && $product->getCarbs() === 0.0
                    && $product->getFats() === 0.0
                    && $product->getFiber() === 0.0
                    && $product->getKcal() === 0;
            }));

        $this->service->createFood($productDTO, $this->user);
    }

    /**
     ********************************************************
     *** Tests for getProductsByMarket method ***************
     ********************************************************
     */

    public function testGetProductsByMarketHumanFormatCapitalizesNames(): void {
        $product = $this->createMock(Products::class);
        $product->method('getProductName')->willReturn('pan de molde');
        $product->method('getMarket')->willReturn('mercadona');
        $product->method('getKcal')->willReturn(250);
        $product->method('getProtein')->willReturn(8.0);
        $product->method('getCarbs')->willReturn(45.0);
        $product->method('getFats')->willReturn(3.0);
        $product->method('getFiber')->willReturn(4.0);
        $product->method('getId')->willReturn(1);
        $product->method('getBrand')->willReturn('Hacendado');

        $repositoryResult = [
            'data' => [$product],
            'currentPage' => 1,
            'totalPages' => 2,
            'total' => 150
        ];

        $this->productsRepository
            ->expects($this->once())
            ->method('getProductsByMarket')
            ->with('mercadona', 0, 100)
            ->willReturn($repositoryResult);

        $result = $this->service->getProductsByMarket(1, 'mercadona', 'human', 100);

        $this->assertEquals('Pan de molde', $result['data'][0][0]);
        $this->assertEquals('Mercadona', $result['data'][0][1]);
        $this->assertEquals(250, $result['data'][0][2]);
        $this->assertEquals(1, $result['pagination']['currentPage']);
        $this->assertTrue($result['pagination']['hasNext']);
        $this->assertFalse($result['pagination']['hasPrevious']);
    }

    public function testGetProductsByMarketRawFormatPreservesCase(): void {
        $product = $this->createMock(Products::class);
        $product->method('getProductName')->willReturn('pan de molde');
        $product->method('getMarket')->willReturn('mercadona');

        $repositoryResult = [
            'data' => [$product],
            'currentPage' => 1,
            'totalPages' => 1,
            'total' => 1
        ];

        $this->productsRepository
            ->method('getProductsByMarket')
            ->willReturn($repositoryResult);

        $result = $this->service->getProductsByMarket(1, 'mercadona', 'raw', 100);

        $this->assertEquals('pan de molde', $result['data'][0][0]);
        $this->assertEquals('mercadona', $result['data'][0][1]);
    }

    #[DataProvider('paginationProvider')]
    public function testGetProductsByMarketPagination(
        int $page,
        int $limit,
        int $expectedOffset,
        int $currentPage,
        int $totalPages,
        bool $expectedHasNext,
        bool $expectedHasPrevious
    ): void {
        $repositoryResult = [
            'data' => [],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'total' => $totalPages * $limit
        ];

        $this->productsRepository
            ->expects($this->once())
            ->method('getProductsByMarket')
            ->with('carrefour', $expectedOffset, $limit)
            ->willReturn($repositoryResult);

        $result = $this->service->getProductsByMarket($page, 'carrefour', 'human', $limit);

        $this->assertEquals($expectedHasNext, $result['pagination']['hasNext']);
        $this->assertEquals($expectedHasPrevious, $result['pagination']['hasPrevious']);
    }

    public function testGetProductsByMarketEmptyResults(): void {
        $repositoryResult = [
            'data' => [],
            'currentPage' => 1,
            'totalPages' => 0,
            'total' => 0
        ];

        $this->productsRepository
            ->expects($this->once())
            ->method('getProductsByMarket')
            ->willReturn($repositoryResult);

        $result = $this->service->getProductsByMarket(1, 'non_existent_market', 'human', 50);

        $this->assertEmpty($result['data']);
        $this->assertEquals(0, $result['pagination']['totalItems']);
        $this->assertFalse($result['pagination']['hasNext']);
        $this->assertFalse($result['pagination']['hasPrevious']);
    }

    /**
     ********************************************************
     *** Tests for searchProducts method ********************
     ********************************************************
     */

    public function testSearchProductsReturnsFormattedArray(): void {
        $product = $this->createMock(Products::class);
        $product->method('getId')->willReturn(10);
        $product->method('getProductName')->willReturn('Leche Semidesnatada');
        $product->method('getMarket')->willReturn('Mercadona');
        $product->method('getKcal')->willReturn(46);

        if (method_exists(ProductsRepository::class, 'searchProducts')) {
            $this->productsRepository
                ->method('searchProducts')
                ->willReturn([$product]);
        }
        $this->productsRepository
            ->method('autocomplete')
            ->willReturn([$product]);

        $result = $this->service->searchProducts('Leche');

        $this->assertCount(1, $result);
        $this->assertEquals([
            'id' => 10,
            'name' => 'Leche Semidesnatada',
            'market' => 'Mercadona',
            'kcal' => 46
        ], $result[0]);
    }

    public function testSearchProductsReturnsEmptyArrayWhenNoMatches(): void {
        if (method_exists(ProductsRepository::class, 'searchProducts')) {
            $this->productsRepository
                ->method('searchProducts')
                ->willReturn([]);
        }
        $this->productsRepository
            ->method('autocomplete')
            ->willReturn([]);

        $result = $this->service->searchProducts('ProductoInexistente');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     ********************************************************
     *** Tests for searchProductsByFullText method **********
     ********************************************************
     */

    public function testSearchProductsByFullTextReturnsFormattedDataAndPagination(): void {
        $product = $this->createMock(Products::class);
        $product->method('getId')->willReturn(5);
        $product->method('getProductName')->willReturn('Atún al natural');
        $product->method('getMarket')->willReturn('Lidl');
        $product->method('getKcal')->willReturn(100);
        $product->method('getProtein')->willReturn(23.0);
        $product->method('getCarbs')->willReturn(0.0);
        $product->method('getFats')->willReturn(1.0);
        $product->method('getFiber')->willReturn(0.0);
        $product->method('getBrand')->willReturn('Nixe');

        $repositoryResult = [
            'data' => [$product],
            'totalPages' => 3,
            'total' => 25
        ];

        $this->productsRepository
            ->expects($this->once())
            ->method('fullTextSearch')
            ->with('Atun', 0, 10)
            ->willReturn($repositoryResult);

        $result = $this->service->searchProductsByFullText('Atun', 1, 10);

        $this->assertCount(1, $result['data']);
        $this->assertEquals([
            'id' => 5,
            'name' => 'Atún al natural',
            'market' => 'Lidl',
            'kcal' => 100,
            'protein' => 23.0,
            'carbs' => 0.0,
            'fats' => 1.0,
            'fiber' => 0.0,
            'brand' => 'Nixe'
        ], $result['data'][0]);

        $this->assertEquals(1, $result['pagination']['currentPage']);
        $this->assertEquals(3, $result['pagination']['totalPages']);
        $this->assertEquals(25, $result['pagination']['totalItems']);
        $this->assertTrue($result['pagination']['hasNext']);
        $this->assertFalse($result['pagination']['hasPrevious']);
    }

    public function testSearchProductsByFullTextEmptyResults(): void {
        $repositoryResult = [
            'data' => [],
            'totalPages' => 0,
            'total' => 0
        ];

        $this->productsRepository
            ->expects($this->once())
            ->method('fullTextSearch')
            ->with('qwerty', 0, 125)
            ->willReturn($repositoryResult);

        $result = $this->service->searchProductsByFullText('qwerty');

        $this->assertEmpty($result['data']);
        $this->assertFalse($result['pagination']['hasNext']);
        $this->assertFalse($result['pagination']['hasPrevious']);
    }

    /**
     ********************************************************
     *** Tests for registerFoodIntake method ****************
     ********************************************************
     */

    public function testRegisterFoodIntakeSuccessCalculatesMacrosAndLogsInfo(): void {
        $intake = ['id' => 12, 'grams' => 150];

        $foundProduct = $this->createMock(Products::class);
        $foundProduct->method('getProductName')->willReturn('Pollo Asado');
        $foundProduct->method('getProtein')->willReturn(20.0);
        $foundProduct->method('getCarbs')->willReturn(0.0);
        $foundProduct->method('getFats')->willReturn(5.0);
        $foundProduct->method('getFiber')->willReturn(0.0);

        $this->productsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 12])
            ->willReturn($foundProduct);

        $this->calorieCalculator
            ->expects($this->once())
            ->method('calorieCalc')
            ->with($foundProduct)
            ->willReturn(125);

        $this->logger
            ->expects($this->once())
            ->method('info')
            ->with($this->stringContains('[FOOD_REGISTRY_SERVICE] Successfully registered food with ID: Pollo Asado'));

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('updateMacroIntake')
            ->with(
                $this->user,
                $this->callback(function (MacroDataDTO $macroDTO) {
                    return $macroDTO->getProtein() === 30.0
                        && $macroDTO->getCarbs() === 0.0
                        && $macroDTO->getFats() === 7.5
                        && $macroDTO->getFiber() === 0.0
                        && $macroDTO->getCalories() === 187.5;
                })
            );

        $result = $this->service->registerFoodIntake($intake, $this->user);

        $this->assertTrue($result);
    }

    public function testRegisterFoodIntakeDefaultsToZeroGramsWhenGramsKeyMissing(): void {
        $intake = ['id' => 12];

        $foundProduct = $this->createMock(Products::class);
        $foundProduct->method('getProductName')->willReturn('Manzana');
        $foundProduct->method('getProtein')->willReturn(0.3);
        $foundProduct->method('getCarbs')->willReturn(14.0);
        $foundProduct->method('getFats')->willReturn(0.2);
        $foundProduct->method('getFiber')->willReturn(2.4);

        $this->productsRepository
            ->method('findOneBy')
            ->willReturn($foundProduct);

        $this->calorieCalculator
            ->method('calorieCalc')
            ->willReturn(52);

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('updateMacroIntake')
            ->with(
                $this->user,
                $this->callback(function (MacroDataDTO $macroDTO) {
                    return $macroDTO->getProtein() === 0.0
                        && $macroDTO->getCarbs() === 0.0
                        && $macroDTO->getFats() === 0.0
                        && $macroDTO->getFiber() === 0.0
                        && $macroDTO->getCalories() === 0.0;
                })
            );

        $result = $this->service->registerFoodIntake($intake, $this->user);

        $this->assertTrue($result);
    }

    public function testRegisterFoodIntakeProductNotFoundReturnsFalseAndLogsWarning(): void {
        $intake = ['id' => 999, 'grams' => 100];

        $this->productsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 999])
            ->willReturn(null);

        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with('[FOOD_REGISTRY_SERVICE] Food not found while registering an intake related to it.');

        $this->kcalsDailyRepository
            ->expects($this->never())
            ->method('updateMacroIntake');

        $result = $this->service->registerFoodIntake($intake, $this->user);

        $this->assertFalse($result);
    }

    public function testRegisterFoodIntakeHandlesExceptionAndLogsError(): void {
        $intake = ['id' => 10, 'grams' => 200];

        $foundProduct = $this->createMock(Products::class);
        $foundProduct->method('getProductName')->willReturn('Arroz Integral');

        $this->productsRepository
            ->method('findOneBy')
            ->willReturn($foundProduct);

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('updateMacroIntake')
            ->willThrowException(new \RuntimeException('Database connection error'));

        $this->logger
            ->expects($this->exactly(2))
            ->method('error')
            ->with($this->stringContains('[FOOD_REGISTRY_SERVICE]'));

        $result = $this->service->registerFoodIntake($intake, $this->user);

        $this->assertFalse($result);
    }

    /**
     ********************************************************
     *** DATA PROVIDERS *************************************
     ********************************************************
     */

    public static function paginationProvider(): array {
        return [
            'firstPage_hasMorePages' => [
                'page' => 1,
                'limit' => 10,
                'expectedOffset' => 0,
                'currentPage' => 1,
                'totalPages' => 5,
                'expectedHasNext' => true,
                'expectedHasPrevious' => false,
            ],
            'middlePage_hasBothDirections' => [
                'page' => 3,
                'limit' => 10,
                'expectedOffset' => 20,
                'currentPage' => 3,
                'totalPages' => 5,
                'expectedHasNext' => true,
                'expectedHasPrevious' => true,
            ],
            'lastPage_noNextPage' => [
                'page' => 5,
                'limit' => 10,
                'expectedOffset' => 40,
                'currentPage' => 5,
                'totalPages' => 5,
                'expectedHasNext' => false,
                'expectedHasPrevious' => true,
            ],
        ];
    }
}
