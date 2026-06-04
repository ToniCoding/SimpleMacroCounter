<?php

namespace Smc\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validation;

use src\DTO\FoodDTO;

class FoodDtoTest extends TestCase {
    #[DataProvider('foodDataProvider')]
    public function testFoodDtoValidation(
        mixed $name,
        mixed $market,
        mixed $protein,
        mixed $carbs,
        mixed $fats,
        mixed $fiber,
        bool $shouldBeValid): void {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new FoodDTO($name, $market, $protein, $carbs, $fats, $fiber);

        $violations = $validator->validate($dto);

        if ($shouldBeValid) {
            $this->assertCount(0, $violations);
        } else {
            $this->assertGreaterThan(0, $violations->count());
        }
    }

    public function testSettersGettersAndGetters(): void {
        $dto = new FoodDTO('', '', 0, 0, 0, 0);
        $dto->setName('test food');
        $dto->setMarket('test market');
        $dto->setProtein(20.15);
        $dto->setCarbs(20.15);
        $dto->setFats(20.15);
        $dto->setFiber(20.15);

        $this->assertSame('test food', $dto->getName());
        $this->assertSame('test market', $dto->getMarket());
        $this->assertSame(20.15, $dto->getProtein());
        $this->assertSame(20.15, $dto->getCarbs());
        $this->assertSame(20.15, $dto->getFats());
        $this->assertSame(20.15, $dto->getFiber());
    }

    public static function foodDataProvider(): array {
        return [
            'correctData' => ['testFood', 'testMarket', 1.00, 2.00, 3.00, 4.00, true],
            'autoStringCasting' => ['testFood', 'testMarket', '1.00', '2.00', '3.00', '4.00', true],
            'negativeZero' => ['testFood', 'testMarket', -0.0, -0.0, -0.0, -0.0, true],
            'zeroValues' => ['test', 'test', 0.0, 0.0, 0.0, 0.0, true],
            'largeValues' => ['test', 'test', 9999.99, 9999.99, 9999.99, 9999.99, true],
            'negativeData' => ['testFood', 'testMarket', 1.00, 2.00, -3.00, 4.00, false],
            'incorrectNameType' => [false, 'testMarket', 1.00, 2.00, 3.00, '4.00', false],
            'incorrectMarketType' => ['testFood', false, 1.00, 2.00, 3.00, 4.00, false],
            'invalidFields' => [false, false, false, false, false, false, false],
            'emptyName' => ['', 'testMarket', 1.00, 2.00, 3.00, 4.00, false],
            'emptyMarket' => ['testFood', '', 1.00, 2.00, 3.00, 4.00, false],
            'nanCase' => ['testFood', '', NAN, NAN, NAN, NAN, false],
            'infiniteCase' => ['testFood', '', INF, INF, INF, INF, false]
        ];
    }
}
