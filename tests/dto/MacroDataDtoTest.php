<?php

namespace Smc\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validation;

use src\DTO\MacroDataDTO;

class MacroDataDtoTest extends TestCase {
    #[DataProvider('foodProvider')]
    public function testLoggedUserDtoValidation(
        mixed $protein,
        mixed $carbs,
        mixed $fats,
        mixed $fiber,
        mixed $calories,
        bool $shouldBeValid): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new MacroDataDTO($protein, $carbs, $fats, $fiber, $calories);

        $violations = $validator->validate($dto);

        if ($shouldBeValid) {
            $this->assertCount(0, $violations);
        } else {
            $this->assertGreaterThan(0, $violations->count());
        }
    }

    public function testSettersGettersAndGetters(): void {
        $dto = new MacroDataDTO(0, 0, 0, 0);
        $dto->setProtein(20.15);
        $dto->setCarbs(20.15);
        $dto->setFats(20.15);
        $dto->setFiber(20.15);
        $dto->setCalories(15.00);

        $this->assertSame(20.15, $dto->getProtein());
        $this->assertSame(20.15, $dto->getCarbs());
        $this->assertSame(20.15, $dto->getFats());
        $this->assertSame(20.15, $dto->getFiber());
        $this->assertSame(15.00, $dto->getCalories());
    }

    #[DataProvider('toStringDataProvider')]
    public function testToString(
        float $protein, 
        float $carbs, 
        float $fats, 
        float $fiber, 
        float $calories, 
        string $expected
    ): void {
        $dto = new MacroDataDTO($protein, $carbs, $fats, $fiber, $calories);
        $this->assertSame($expected, (string) $dto);
    }

    public static function toStringDataProvider(): array {
        return [
            'zeroValues' => [
                0, 0, 0, 0, 0,
                "Calories: 0\n\tProtein: 0\n\tCarbs: 0\n\tFats: 0\n\tFiber: 0"
            ],
            'decimalValues' => [
                20.5, 30.0, 15.2, 5.0, 350,
                "Calories: 350\n\tProtein: 20.5\n\tCarbs: 30\n\tFats: 15.2\n\tFiber: 5"
            ],
            'integerValues' => [
                20, 30, 15, 5, 350,
                "Calories: 350\n\tProtein: 20\n\tCarbs: 30\n\tFats: 15\n\tFiber: 5"
            ],
        ];
    }

    public static function foodProvider(): array {
        return [
            'correctData' => [1.00, 2.00, 3.00, 4.00, 100, true],
            'dataCast' => ['1.00', '2.00', '3.00', '4.00', '100', true],
            'zeroData' => [0.00, 0.00, 0.00, 0.00, 000, true],
            'negativeCalories' => [1.00, 2.00, 3.00, 4.00, -1, false],
            'negativeProtein' => [-1.00, 2.00, 3.00, 4.00, 100, false],
            'negativeCarbs' => [1.00, -2.00, 3.00, 4.00, 100, false],
            'negativeFats' => [1.00, 2.00, -3.00, 4.00, 100, false],
            'negativeFiber' => [1.00, 2.00, 3.00, -4.00, 100, false],
        ];
    }
}
