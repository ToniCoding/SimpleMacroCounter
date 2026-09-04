<?php

namespace Smc\Tests\Service;

use App\DTO\DailyIntakeDTO;
use App\DTO\MacroSettingsDTO;
use App\DTO\UserGoalsDTO;
use App\Entity\KcalsDaily;
use App\Entity\User;
use App\Entity\UserGoals;
use App\Exceptions\WriteToDatabaseException;
use App\Repository\KcalsDailyRepository;
use App\Repository\UserGoalsRepository;
use App\Service\DailyIntakeRecordService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DailyIntakeRecordServiceTest extends TestCase {
    private $user;
    private $kcalsDailyRepository;
    private $userGoalsRepository;
    private $params;
    private $loggerInterface;
    private DailyIntakeRecordService $service;

    public function setUp(): void {
        $this->user = $this->createMock(User::class);
        $this->kcalsDailyRepository = $this->createMock(KcalsDailyRepository::class);
        $this->userGoalsRepository = $this->createMock(UserGoalsRepository::class);
        $this->params = $this->createMock(ParameterBagInterface::class);
        $this->loggerInterface = $this->createMock(LoggerInterface::class);

        $this->params
            ->method('get')
            ->willReturnMap([
                ['nutrition.default_calories', 2000],
                ['nutrition.default_protein', 150],
                ['nutrition.default_carbs', 200],
                ['nutrition.default_carb', 200],
                ['nutrition.default_fat', 70],
                ['nutrition.default_fats', 70],
                ['nutrition.default_fiber', 30],
                ['nutrition.minimum_calories', 1000.0],
                ['nutrition.minimum_protein', 30.0],
                ['nutrition.minimum_carbs', 50.0],
                ['nutrition.minimum_carb', 50.0],
                ['nutrition.minimum_fat', 10.0],
                ['nutrition.minimum_fats', 10.0],
                ['nutrition.minimum_fiber', 5.0],
            ]);

        $this->service = new DailyIntakeRecordService(
            $this->kcalsDailyRepository,
            $this->userGoalsRepository,
            $this->params,
            $this->loggerInterface
        );
    }

    /**
     ********************************************************
     *** Tests for ensureDailyIntakeRecord method ***********
     ********************************************************
     */

    public function testEnsureDailyIntakeRecord(): void {
        $existingMacroRecord = $this->createMock(KcalsDaily::class);
        
        $this->loggerInterface
            ->expects($this->exactly(2))
            ->method('info')
            ->with($this->stringContains('[DailyIntakeRecordService]'));

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('findIntakeRegistryForToday')
            ->willReturn($existingMacroRecord);

        $testedMethodResult = $this->service->ensureDailyIntakeRecord($this->user);

        $this->assertInstanceOf(DailyIntakeDTO::class, $testedMethodResult);
    }

    public function testEnsureDailyIntakeRecordSuccessAfterNotFindingExistingRecord(): void {
        $this->loggerInterface
            ->expects($this->exactly(2))
            ->method('info')
            ->with($this->stringContains('[DailyIntakeRecordService]'));

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('findIntakeRegistryForToday')
            ->willReturn(null);

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('insertIntakeRegistry')
            ->willReturn(true);

        $testedMethodResult = $this->service->ensureDailyIntakeRecord($this->user);

        $this->assertInstanceOf(DailyIntakeDTO::class, $testedMethodResult);
    }

    public function testEnsureDailyIntakeRecordThrowsExceptionWritingToDatabase(): void {
        $this->expectException(WriteToDatabaseException::class);

        $this->loggerInterface
            ->expects($this->exactly(1))
            ->method('info')
            ->with($this->stringContains('[DailyIntakeRecordService]'));

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

    /**
     ********************************************************
     *** Tests for ensureOneMacroGoal method ****************
     ********************************************************
     */

    public function testEnsureOneMacroGoal(): void {
        $this->loggerInterface
            ->expects($this->exactly(2))
            ->method('info')
            ->with($this->stringContains('[DailyIntakeRecordService]'));

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('insertGoalRegistry')
            ->willReturn(true);

        $testedMethodResult = $this->service->ensureOneMacroGoal($this->user);

        $this->assertInstanceOf(UserGoalsDTO::class, $testedMethodResult);
    }

    public function testEnsureOneMacroGoalWhenRecordAlreadyExists(): void {
        $existingGoalRecord = $this->createMock(UserGoals::class);
        $existingGoalRecord->method('getCalories')->willReturn(2000);
        $existingGoalRecord->method('getProtein')->willReturn('150.00');
        $existingGoalRecord->method('getCarbs')->willReturn('200.00');
        $existingGoalRecord->method('getFats')->willReturn('70.00');
        $existingGoalRecord->method('getFiber')->willReturn('30.00');
    
        $this->loggerInterface
            ->expects($this->exactly(2))
            ->method('info')
            ->with($this->stringContains('[DailyIntakeRecordService]'));
    
        $this->userGoalsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['user' => $this->user])
            ->willReturn($existingGoalRecord);
    
        $this->userGoalsRepository
            ->expects($this->never())
            ->method('insertGoalRegistry');
    
        $testedMethodResult = $this->service->ensureOneMacroGoal($this->user);
    
        $this->assertInstanceOf(UserGoalsDTO::class, $testedMethodResult);
        $this->assertEquals(2000, $testedMethodResult->getCalories());
        $this->assertEquals('150.00', $testedMethodResult->getProtein());
    }

    public function testEnsureOneMacroGoalThrowsExceptionWhileWritingInDatabase(): void {
        $this->expectException(WriteToDatabaseException::class);

        $this->loggerInterface
            ->expects($this->exactly(1))
            ->method('info')
            ->with($this->stringContains('[DailyIntakeRecordService]'));

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('insertGoalRegistry')
            ->willReturn(false);

        $this->service->ensureOneMacroGoal($this->user);
    }

    /**
     ********************************************************
     *** Tests for modifyMacroGoal method *******************
     ********************************************************
     */

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
        // Instantiate MacroSettingsDTO with correct argument order: (protein, carbs, fats, fiber, calories)
        $macroSettingsDto = new MacroSettingsDTO(
            $protein ?? 0.0,
            $carbs ?? 0.0,
            $fats ?? 0.0,
            $fiber ?? 0.0,
            $calories ?? 0.0
        );

        $minCalories = 1000.0;
        $minProtein = 30.0;
        $minCarbs = 50.0;
        $minFats = 10.0;
        $minFiber = 5.0;

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('updateGoalRegistry')
            ->with(
                $this->user,
                $this->callback(function (array $validatedData) use (
                    $calories, $protein, $carbs, $fats, $fiber,
                    $minCalories, $minProtein, $minCarbs, $minFats, $minFiber
                ) {
                    $c  = $calories ?? 0.0;
                    $p  = $protein ?? 0.0;
                    $ca = $carbs ?? 0.0;
                    $f  = $fats ?? 0.0;
                    $fi = $fiber ?? 0.0;

                    $expectedCalories = ($c != 0.0 && $c < $minCalories) ? $minCalories : $c;
                    $expectedProtein  = ($p != 0.0 && $p < $minProtein)   ? $minProtein  : $p;
                    $expectedCarbs    = ($ca != 0.0 && $ca < $minCarbs)   ? $minCarbs    : $ca;
                    $expectedFats     = ($f != 0.0 && $f < $minFats)     ? $minFats     : $f;
                    $expectedFiber    = ($fi != 0.0 && $fi < $minFiber)   ? $minFiber    : $fi;

                    return (float)$validatedData['calories'] == (float)$expectedCalories
                        && (float)$validatedData['protein'] == (float)$expectedProtein
                        && (float)$validatedData['carbs'] == (float)$expectedCarbs
                        && (float)$validatedData['fats'] == (float)$expectedFats
                        && (float)$validatedData['fiber'] == (float)$expectedFiber;
                })
            )
            ->willReturn($shouldUpdate);

        $testedMethodResult = $this->service->modifyMacroGoal($this->user, $macroSettingsDto);

        $this->assertSame($shouldUpdate, $testedMethodResult);
    }

    public function testModifyMacroGoalReturnsFalseWhenDatabaseUpdateFails(): void {
        $macroSettingsDto = new MacroSettingsDTO(180.0, 250.0, 70.0, 35.0, 2500.0);

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('updateGoalRegistry')
            ->willReturn(false);

        $result = $this->service->modifyMacroGoal($this->user, $macroSettingsDto);

        $this->assertFalse($result);
    }

    public function testModifyMacroGoalThrowsExceptionOnRepositoryFailure(): void {
        $this->expectException(WriteToDatabaseException::class);

        $macroSettingsDto = new MacroSettingsDTO(180.0, 250.0, 70.0, 35.0, 2500.0);

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('updateGoalRegistry')
            ->willThrowException(new WriteToDatabaseException('Database connection failed.'));

        $this->service->modifyMacroGoal($this->user, $macroSettingsDto);
    }

    /**
     ********************************************************
     *** DATA PROVIDERS *************************************
     ********************************************************
     */
    public static function macroSettingsProvider(): array {
        return [
            'happyPath_allValidValues' => [
                'calories' => 2500.0,
                'protein' => 180.0,
                'carbs' => 250.0,
                'fats' => 70.0,
                'fiber' => 35.0,
                'expectedLoopIterations' => 5,
                'shouldUpdate' => true,
            ],
            'partialZeros_someMacrosIgnored' => [
                'calories' => 2000.0,
                'protein' => 0.0,
                'carbs' => 200.0,
                'fats' => 60.0,
                'fiber' => 0.0,
                'expectedLoopIterations' => 3,
                'shouldUpdate' => true,
            ],
            'allZeros_noUpdateExpected' => [
                'calories' => 0.0,
                'protein' => 0.0,
                'carbs' => 0.0,
                'fats' => 0.0,
                'fiber' => 0.0,
                'expectedLoopIterations' => 0,
                'shouldUpdate' => false,
            ],
            'exactMinimums_boundaryValues' => [
                'calories' => 1000.0,
                'protein' => 30.0,
                'carbs' => 50.0,
                'fats' => 10.0,
                'fiber' => 5.0,
                'expectedLoopIterations' => 5,
                'shouldUpdate' => true,
            ],
            'belowMinimums_shouldBeCorrectedToMin' => [
                'calories' => 500.0,
                'protein' => 15.0,
                'carbs' => 20.0,
                'fats' => 5.0,
                'fiber' => 2.0,
                'expectedLoopIterations' => 5,
                'shouldUpdate' => true,
            ],
            'mixedValues_zerosMinimaAndValid' => [
                'calories' => 0.0,
                'protein' => 10.0,
                'carbs' => 150.0,
                'fats' => 0.0,
                'fiber' => 1.0,
                'expectedLoopIterations' => 3,
                'shouldUpdate' => true,
            ],
            'negativeValues_shouldBeCorrectedToMin' => [
                'calories' => -500.0,
                'protein' => 180.0,
                'carbs' => 200.0,
                'fats' => 60.0,
                'fiber' => 25.0,
                'expectedLoopIterations' => 5,
                'shouldUpdate' => true,
            ],
            'nullValue_firstParameterNull' => [
                'calories' => null,
                'protein' => 100.0,
                'carbs' => 200.0,
                'fats' => 60.0,
                'fiber' => 25.0,
                'expectedLoopIterations' => 4,
                'shouldUpdate' => true,
            ],
            'nullValue_lastParameterNull' => [
                'calories' => 2500.0,
                'protein' => 180.0,
                'carbs' => 200.0,
                'fats' => 60.0,
                'fiber' => null,
                'expectedLoopIterations' => 4,
                'shouldUpdate' => true,
            ],
            'allNulls_noUpdateExpected' => [
                'calories' => null,
                'protein' => null,
                'carbs' => null,
                'fats' => null,
                'fiber' => null,
                'expectedLoopIterations' => 0,
                'shouldUpdate' => false,
            ],
        ];
    }
}
