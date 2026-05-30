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

    public static function foodProvider(): array {
        return [
            'correctData' => [1.00, 2.00, 3.00, 4.00, 100, true],
            'dataCast' => ['1.00', '2.00', '3.00', '4.00', '100', true],
            'negativeCalories' => [1.00, 2.00, 3.00, 4.00, -1, false],
            'negativeProtein' => [-1.00, 2.00, 3.00, 4.00, 100, false],
            'negativeCarbs' => [1.00, -2.00, 3.00, 4.00, 100, false],
            'negativeFats' => [1.00, 2.00, -3.00, 4.00, 100, false],
            'negativeFiber' => [1.00, 2.00, 3.00, -4.00, 100, false],
        ];
    }
}
