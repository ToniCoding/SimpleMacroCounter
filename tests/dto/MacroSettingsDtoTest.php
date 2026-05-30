<?php

namespace Smc\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validation;

use src\DTO\MacroSettingsDTO;

class MacroSettingsDtoTest extends TestCase {
    #[DataProvider('foodProvider')]
    public function testMacroSettingsDto(
        mixed $newProtein,
        mixed $newCarbs,
        mixed $newFats,
        mixed $newFiber,
        mixed $newCalories,
        bool $shouldBeValid): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new MacroSettingsDTO($newProtein, $newCarbs, $newFats, $newFiber, $newCalories);

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
