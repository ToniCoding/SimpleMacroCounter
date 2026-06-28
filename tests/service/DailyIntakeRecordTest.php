<?php

namespace Smc\Tests\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use App\DTO\MacroSettingsDTO;
use App\Entity\KcalsDaily;
use App\Entity\User;
use App\Entity\UserGoals;
use App\Exceptions\UnrecognizedMacroException;
use App\Exceptions\WriteToDatabaseException;
use App\Repository\KcalsDailyRepository;
use App\Repository\UserGoalsRepository;
use App\Service\DailyIntakeRecord;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DailyIntakeRecordTest extends TestCase
{
    private $user;
    private $kcalsDailyRepository;
    private $userGoalsRepository;
    private $params;
    private $loggerInterface;
    private DailyIntakeRecord $service;

    protected function setUp(): void {
        $this->user = $this->createMock(User::class);
        $this->kcalsDailyRepository = $this->createMock(KcalsDailyRepository::class);
        $this->userGoalsRepository = $this->createMock(UserGoalsRepository::class);
        $this->params = $this->createMock(ParameterBagInterface::class);
        $this->loggerInterface = $this->createMock(LoggerInterface::class);

        $this->service = new DailyIntakeRecord(
            $this->kcalsDailyRepository,
            $this->userGoalsRepository,
            $this->params,
            $this->loggerInterface
        );
    }

    public function testEnsureDailyIntakeRecordHappyPath(): void {
        $existingMacroRecord = $this->createMock(KcalsDaily::class);

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('findIntakeRegistryForToday')
            ->willReturn($existingMacroRecord);

        $result = $this->service->ensureDailyIntakeRecord($this->user);

        $this->assertSame($existingMacroRecord, $result);
    }

    public function testEnsureDailyIntakeRecordDatabaseWriteException(): void {
        $this->expectException(WriteToDatabaseException::class);
        $this->expectExceptionMessage('There was an error inserting a new intake registry.');

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('findIntakeRegistryForToday')
            ->willReturn(null);

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('insertIntakeRegistry')
            ->willReturn(false);

        $this->service->ensureDailyIntakeRecord($this->user);
    }

    public function testEnsureDailyIntakeRecordInsertionSuccess(): void {
        $newRecord = $this->createMock(KcalsDaily::class);

        $this->kcalsDailyRepository
            ->expects($this->exactly(2))
            ->method('findIntakeRegistryForToday')
            ->willReturnOnConsecutiveCalls(null, $newRecord);

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('insertIntakeRegistry')
            ->willReturn(true);

        $this->loggerInterface
            ->expects($this->exactly(2))
            ->method('info');

        $result = $this->service->ensureDailyIntakeRecord($this->user);

        $this->assertSame($newRecord, $result);
    }

    #[DataProvider('macroSettingsProvider')]
    public function testModifyMacroGoal(
        ?float $calories,
        ?float $protein,
        ?float $carbs,
        ?float $fats,
        ?float $fiber,
        int $expectedLoopIterations,
        bool $shouldUpdate
    ): void {
        if ($calories === null || $protein === null || $carbs === null || $fats === null || $fiber === null) {
            $this->expectException(UnrecognizedMacroException::class);
            return;
        }
        
        $macroSettingsDto = new MacroSettingsDTO(
            $protein ?? 0,
            $carbs ?? 0,
            $fats ?? 0,
            $fiber ?? 0,
            $calories ?? 0
        );

        $this->params
            ->method('get')
            ->willReturnMap([
                ['nutrition.minimum_calories', 1000],
                ['nutrition.minimum_protein', 30],
                ['nutrition.minimum_carb', 50],
                ['nutrition.minimum_fat', 10],
                ['nutrition.minimum_fiber', 5],
            ]);

        if (!$shouldUpdate && $expectedLoopIterations === 0) {
            $this->userGoalsRepository
                ->expects($this->never())
                ->method('updateGoalRegistry');
            
            return;
        }

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('updateGoalRegistry')
            ->with(
                $this->user,
                $this->callback(function ($validatedData) use ($calories, $protein, $carbs, $fats, $fiber) {
                    return ($validatedData['calories'] ?? null) === ($calories > 0 ? (int) $calories : null)
                        && ($validatedData['protein'] ?? null) === ($protein > 0 ? (string) $protein : null)
                        && ($validatedData['carbs'] ?? null) === ($carbs > 0 ? (string) $carbs : null)
                        && ($validatedData['fats'] ?? null) === ($fats > 0 ? (string) $fats : null)
                        && ($validatedData['fiber'] ?? null) === ($fiber > 0 ? (string) $fiber : null);
                    }
                )
            );

        $this->service->modifyMacroGoal($this->user, $macroSettingsDto);
    }

    public function testEnsureOneMacroGoalWhenExists(): void {
        $existingGoal = $this->createMock(UserGoals::class);

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('findGoalsRegistry')
            ->with($this->user)
            ->willReturn($existingGoal);

        $result = $this->service->ensureOneMacroGoal($this->user);

        $this->assertSame($existingGoal, $result);
    }

    public function testEnsureOneMacroGoalWhenNotExists(): void {
        $this->userGoalsRepository
            ->expects($this->exactly(2))
            ->method('findGoalsRegistry')
            ->willReturnOnConsecutiveCalls(null, $this->createMock(UserGoals::class));

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('insertGoalRegistry');

        $this->params
            ->method('get')
            ->willReturnMap([
                ['nutrition.default_calories', 2000],
                ['nutrition.default_protein', 150],
                ['nutrition.default_carb', 200],
                ['nutrition.default_fat', 65],
                ['nutrition.default_fiber', 30],
            ]);

        $result = $this->service->ensureOneMacroGoal($this->user);

        $this->assertInstanceOf(UserGoals::class, $result);
    }

    public static function macroSettingsProvider(): array {
        return [
            'happyPath' => [
                'calories' => 2500,
                'protein' => 180,
                'carbs' => 250,
                'fats' => 70,
                'fiber' => 35,
                'expectedLoopIterations' => 5,
                'shouldUpdate' => true
            ],
            'someZeroValue' => [
                'calories' => 2000,
                'protein' => 0,
                'carbs' => 200,
                'fats' => 60,
                'fiber' => 25,
                'expectedLoopIterations' => 4,
                'shouldUpdate' => true
            ],
            'allZeroValues' => [
                'calories' => 0,
                'protein' => 0,
                'carbs' => 0,
                'fats' => 0,
                'fiber' => 0,
                'expectedLoopIterations' => 0,
                'shouldUpdate' => false
            ],
            'minimumValues' => [
                'calories' => 1000,
                'protein' => 30,
                'carbs' => 50,
                'fats' => 10,
                'fiber' => 5,
                'expectedLoopIterations' => 5,
                'shouldUpdate' => true
            ],
            /**
             * ######################################################
             * ###### NOTE #########################################
             * ######################################################
             * This test cases are discarded and left out the test runs for now until the
             * service is refactored to make more sense. Jira Key: NEF-27.
             */
            // 'nullValue' => [
            //     'calories' => null,
            //     'protein' => 0,
            //     'carbs' => 200,
            //     'fats' => 60,
            //     'fiber' => 25,
            //     'expectedLoopIterations' => 1,
            //     'shouldUpdate' => false
            // ],
            // 'finalNullValue' => [
            //     'calories' => 2500,
            //     'protein' => 0,
            //     'carbs' => 200,
            //     'fats' => 60,
            //     'fiber' => null,
            //     'expectedLoopIterations' => 4,
            //     'shouldUpdate' => false
            // ]
        ];
    }
}
