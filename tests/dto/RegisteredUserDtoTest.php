<?php

namespace Smc\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validation;

use src\DTO\RegisterUserDTO;

class RegisteredUserDtoTest extends TestCase {
    #[DataProvider('userProvider')]
    public function testLoggedUserDto(
        mixed $username,
        mixed $password,
        mixed $email,
        mixed $alias,
        mixed $age,
        bool $shouldBeValid
    ): void {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $dto = new RegisterUserDTO($username, $password, $email, $alias, $age);

        $violations = $validator->validate($dto);

        if ($shouldBeValid) {
            $this->assertCount(0, $violations);
        } else {
            $this->assertGreaterThan(0, $violations->count());
        }
    }

    public function testGettersAndSetters(): void {
        $dto = new RegisterUserDTO();

        $dto->setUsername('test_user');
        $dto->setPassword('1234');
        $dto->setEmail('test@smc.dev');
        $dto->setAge(25);
        $dto->setAlias('test_alias');

        $this->assertSame('test_user', $dto->getUsername());
        $this->assertSame('1234', $dto->getPassword());
        $this->assertSame('test@smc.dev', $dto->getEmail());
        $this->assertSame(25, $dto->getAge());
        $this->assertSame('test_alias', $dto->getAlias());
    }

    public static function userProvider(): array {
        return  [
            'correctCase' => ['testUser', 'testPassword', 'test@email.net', 'testAlias', 20, true],
            'castingCase' => ['testUser', 'testPassword', 'test@email.net', 'testAlias', '20', true],
            'minimumAge' => ['testUser', 'testPassword', 'test@email.net', 'testAlias', 15, true],
            'maximumAge' => ['testUser', 'testPassword', 'test@email.net', 'testAlias', 100, true],
            'negativeAge' => ['testUser', 'testPassword', 'test@email.net', 'testAlias', -1, false],
            'underAllowedAge' => ['testUser', 'testPassword', 'test@email.net', 'testAlias', 14, false],
            'overAllowedAge' => ['testUser', 'testPassword', 'test@email.net', 'testAlias', 101, false],
            'notComplayingEmail' => ['testUser', 'testPassword', 'notAnEmail', 'testAlias', 101, false],
            'invalidUsername' => [false, 'testPassword', 'notAnEmail', 'testAlias', 101, false],
            'invalidPassword' => ['testUser', false, 'notAnEmail', 'testAlias', 101, false],
            'invalidEmail' => ['testUser', 'testPassword', false, 'testAlias', 101, false],
            'invalidAlias' => ['testUser', 'testPassword', 'notAnEmail', false, 101, false],
        ];
    }
}
