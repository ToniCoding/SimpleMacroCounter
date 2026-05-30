<?php

namespace Smc\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validation;

use src\DTO\LoggedUserDTO;

class LoggedUserDtoTest extends TestCase {
    #[DataProvider('userProvider')]
    public function testLoggedUserDto(
        mixed $username,
        mixed $password,
        bool $shouldBeValid
    ): void {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new LoggedUserDTO($username, $password);

        $violations = $validator->validate($dto);

        if ($shouldBeValid) {
            $this->assertCount(0, $violations);
        } else {
            $this->assertGreaterThan(0, $violations->count());
        }
    }

    public static function userProvider(): array {
        return  [
            'correctCase' =>  ['testUser', 'testPassword', true],
            'castCase' => ['testUser', 1234, true],
            'incorrectUsername' => [false, 'testPassword', false],
            'incorrectPassword' => ['testUser', false, false],
        ];
    }
}
