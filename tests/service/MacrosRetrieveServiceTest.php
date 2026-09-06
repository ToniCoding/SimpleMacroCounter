<?php

namespace Smc\Tests\Service;

use App\DTO\DailyIntakeDTO;
use App\DTO\MacroSettingsDTO;
use App\DTO\UserGoalsDTO;
use App\Entity\KcalsDaily;
use App\Entity\User;
use App\Entity\UserGoals;
use App\Exceptions\NoRecordFoundException;
use App\Exceptions\WriteToDatabaseException;
use App\Helpers\DateParser;
use App\Repository\KcalsDailyRepository;
use App\Repository\UserGoalsRepository;
use App\Service\DailyIntakeRecordService;
use App\Service\MacrosRetrieveService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MacrosRetrieveServiceTest extends TestCase {
    private $user;
    private $kcalsDailyRepository;
    private $userGoalsRepository;
    private $loggerInterface;
    private $dateParser;
    private $dailyIntakeRecordService;
    private MacrosRetrieveService $service;

    public function setUp(): void {
        $this->user = $this->createMock(User::class);
        $this->dailyIntakeRecordService = $this->createMock(DailyIntakeRecordService::class);
        $this->kcalsDailyRepository = $this->createMock(KcalsDailyRepository::class);
        $this->userGoalsRepository = $this->createMock(UserGoalsRepository::class);
        $this->dateParser = $this->createMock(DateParser::class);
        $this->loggerInterface = $this->createMock(LoggerInterface::class);

        $this->service = new MacrosRetrieveService(
            $this->dailyIntakeRecordService,
            $this->kcalsDailyRepository,
            $this->userGoalsRepository,
            $this->dateParser,
            $this->loggerInterface
        );
    }

    /**
     ********************************************************
     *** Tests for calculateUserProgress method ***********
     ********************************************************
     */

    // public function testCalculateUserProgress(): void {
    //     $expectedArrayKeys = ['caloriesProgress', 'proteinProgress', 'carbsProgress', 'fatsProgress', 'fiberProgress'];
        
    //     $macroGramsConsumed = new DailyIntakeDTO(0, 0, 0, 0, 0);
    //     $macroIntakeGoal = new UserGoalsDTO(0, 0, 0, 0, 0);

    //     $this->dailyIntakeRecordService
    //         -> expects($this->once())
    //         -> method('ensureDailyIntakeRecord')
    //         -> willReturn($macroGramsConsumed);

    //     $this->dailyIntakeRecordService
    //         -> expects($this->once())
    //         -> method('ensureOneMacroGoal')
    //         -> willReturn($macroIntakeGoal);

    //     $testedMethodResult = $this->service->calculateUserProgress($this->user);

    //     $this->assertCount(5, $testedMethodResult);

    //     foreach ($expectedArrayKeys as $arrayKey) {
    //         $this->assertArrayHasKey($arrayKey, $testedMethodResult);
    //     }
    // }

    public function testCalculateUserProgress(): void{
        $user = $this->createMock(User::class);

        $dailyRecord = $this->createMock(DailyIntakeDTO::class);

        $dailyRecord->method('__toArray')->willReturn([
            'protein' => 50.0,
            'carbs'   => 150.0,
            'fat'     => 0.0,
        ]);

        $macroGoal = $this->createMock(UserGoalsDTO::class);

        $macroGoal->method('__toArray')->willReturn([
            'protein' => 100.0,
            'carbs'   => 200.0,
            'fat'     => 70.0,
        ]);

        $this->dailyIntakeRecordService
            ->method('ensureDailyIntakeRecord')
            ->with($user)
            ->willReturn($dailyRecord);

        $this->dailyIntakeRecordService
            ->method('ensureOneMacroGoal')
            ->with($user)
            ->willReturn($macroGoal);

        $result = $this->service->calculateUserProgress($user);

        $this->assertSame([
            'proteinProgress' => 50.0,
            'carbsProgress'   => 75.0,
            'fatProgress'     => 0.0,
        ], $result);
    }

    /**
     ********************************************************
     *** Tests for getDataFromPreviousDays method ***********
     ********************************************************
     */

    /**
     * Test for getting data from previous dates with only one user on each day.
     * @return void
     */
    public function testGetDataFromPreviousDaysGroupsRecordsByDate(): void {
        $user = $this->createMock(User::class);
        $previousDays = 7;

        // Dates for two different days
        $date1 = new \DateTimeImmutable('2026-09-01');
        $date2 = new \DateTimeImmutable('2026-09-02');

        // 1 registry per day
        $row1 = $this->createMock(KcalsDaily::class);
        $row1->method('getDate')->willReturn($date1);

        $row2 = $this->createMock(KcalsDaily::class);
        $row2->method('getDate')->willReturn($date2);

        $dbData = [$row1, $row2];

        // Repository mock
        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('findIntakeRegistryForDateRange')
            ->with($user, $previousDays)
            ->willReturn($dbData);

        $result = $this->service->getDataFromPreviousDays($user, $previousDays);

        // Structure assertions
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('2026-09-01', $result);
        $this->assertArrayHasKey('2026-09-02', $result);

        // Assertions for day 1 (exactly 1 record)
        $this->assertCount(1, $result['2026-09-01']);
        $this->assertSame($row1, $result['2026-09-01'][0]);

        // Assertions for day 2 (exactly 1 record)
        $this->assertCount(1, $result['2026-09-02']);
        $this->assertSame($row2, $result['2026-09-02'][0]);
    }

    /**
     * Test for getting data from previous dates with several users on the same day.
     * @return void
     */
    public function testGetDataFromPreviousDaysOnlyReturnsDataForGivenUserOnSameDate(): void {
            $user1 = $this->createMock(User::class);
            $user2 = $this->createMock(User::class);
            $previousDays = 7;

            $date = new \DateTimeImmutable('2026-09-01');

            // Registry for first user.
            $rowUser1 = $this->createMock(KcalsDaily::class);
            $rowUser1->method('getDate')->willReturn($date);
            $rowUser1->method('getUser')->willReturn($user1);

            // Registry for second user, same date as first user.
            $rowUser2 = $this->createMock(KcalsDaily::class);
            $rowUser2->method('getDate')->willReturn($date);
            $rowUser2->method('getUser')->willReturn($user2);

            // Assert that the the method only brings back one registry for each user.
            $this->kcalsDailyRepository
                ->expects($this->once())
                ->method('findIntakeRegistryForDateRange')
                ->with($user1, $previousDays)
                ->willReturn([$rowUser1]);

            $result = $this->service->getDataFromPreviousDays($user1, $previousDays);

            // Structure assertion.
            $this->assertIsArray($result);
            $this->assertCount(1, $result);
            $this->assertArrayHasKey('2026-09-01', $result);

            // Verify that for that date there is only one record for one user (avoid duplicity).
            $this->assertCount(1, $result['2026-09-01']);
            $this->assertSame($rowUser1, $result['2026-09-01'][0]);
            $this->assertNotSame($rowUser2, $result['2026-09-01'][0]);
        }

    /**
     ********************************************************
     *** Tests for getCaloriesConsumedForThisWeek method ****
     ********************************************************
     */

    /**
     * Test calories for this week happy path with 3 days and sum the calories.
     * @return void
     */
    public function testGetCaloriesConsumedForThisWeekCalculatesTotalSum(): void {
        $user = $this->createMock(User::class);

        // Mock several days with caloric data.
        $day1 = $this->createMock(KcalsDaily::class);
        $day1->method('getKcals')->willReturn(2000);

        $day2 = $this->createMock(KcalsDaily::class);
        $day2->method('getKcals')->willReturn(1800);

        $day3 = $this->createMock(KcalsDaily::class);
        $day3->method('getKcals')->willReturn(2200);

        $records = [$day1, $day2, $day3];

        // Repository mock.
        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('findByDateRange')
            ->with(
                $this->isInstanceOf(\DateTimeInterface::class),
                $this->isInstanceOf(\DateTimeInterface::class),
                $user
            )
            ->willReturn($records);

        $totalCalories = $this->service->getCaloriesConsumedForThisWeek($user);

        $this->assertSame(6000, $totalCalories);
    }

    /**
     * Test for no caloric registries this week.
     * @return void
     */
    public function testGetCaloriesConsumedForThisWeekReturnsZeroWhenNoRecordsFound(): void {
        $user = $this->createMock(User::class);

        $this->kcalsDailyRepository
            ->expects($this->once())
            ->method('findByDateRange')
            ->with(
                $this->isInstanceOf(\DateTimeInterface::class),
                $this->isInstanceOf(\DateTimeInterface::class),
                $user
            )
            ->willReturn([]);

        $totalCalories = $this->service->getCaloriesConsumedForThisWeek($user);

        $this->assertSame(0, $totalCalories);
    }

    /**
     ********************************************************
     *** Tests for getWeeklyCalorieGoal method **************
     ********************************************************
     */

    /**
     * Test the calculating the user weekly caloric goal.
     * @return void
     */
    public function testGetWeeklyCalorieGoalSuccess(): void {
        $user = $this->createMock(User::class);

        $userGoalRecord = $this->createMock(UserGoals::class);
        $userGoalRecord->expects($this->once())
            ->method('getCalories')
            ->willReturn(2000);

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['user' => $user])
            ->willReturn($userGoalRecord);

        $weeklyGoal = $this->service->getWeeklyCalorieGoal($user);

        $this->assertSame(14000, $weeklyGoal);
    }

    /**
     * Test exception when the user did not register any caloric record during the week.
     * @return void
     */
    public function testGetWeeklyCalorieGoalThrowsExceptionWhenNoRecordFound(): void  {
        $user = $this->createMock(User::class);

        $this->userGoalsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['user' => $user])
            ->willReturn(null);

        $this->expectException(NoRecordFoundException::class);

        $this->service->getWeeklyCalorieGoal($user);
    }

    /**
     ********************************************************
     *** Tests for calculateWeeklyRisk method **************
     ********************************************************
     */

    #[DataProvider('provideWeeklyRiskCases')]
    public function testCalculateWeeklyRisk(
        int|string $currentDay,
        float $weeklyGoal,
        float $weeklyConsumption,
        float $todayConsumption,
        array $expectedOutput
    ): void {
        $this->dateParser
            ->method('getCurrentWeekDay')
            ->willReturn((string) $currentDay); // Cast a string para cumplir la firma del método

        $result = $this->service->calculateWeeklyRisk($weeklyGoal, $weeklyConsumption, $todayConsumption);

        $this->assertSame($expectedOutput['risk'], $result['risk']);
        $this->assertSame($expectedOutput['expectedConsumption'], $result['expected_consumption']);
        $this->assertSame($expectedOutput['remainingBudget'], $result['remaining_budget']);

        $this->assertArrayHasKey('level', $result);
        $this->assertArrayHasKey('risk_color', $result);
    }

    public static function provideWeeklyRiskCases(): array {
        return [
            'mondayNoPreviousConsumption' => [
                'currentDay' => '1',
                'weeklyGoal' => 14000.0,
                'weeklyConsumption' => 0.0,
                'todayConsumption' => 0.0,
                'expectedOutput' => [
                    'risk' => 1.0,
                    'expectedConsumption' => 14000.0,
                    'remainingBudget' => 14000.0,
                ],
            ],
            'mondayWithTodayConsumption' => [
                'currentDay' => '1',
                'weeklyGoal' => 14000.0,
                'weeklyConsumption' => 0.0,
                'todayConsumption' => 2000.0,
                'expectedOutput' => [
                    'risk' => 1.17,
                    'expectedConsumption' => 14000.0,
                    'remainingBudget' => 12000.0,
                ],
            ],
            'fridayPerfectPace' => [
                'currentDay' => '5',
                'weeklyGoal' => 14000.0,
                'weeklyConsumption' => 8000.0,
                'todayConsumption' => 0.0,
                'expectedOutput' => [
                    'risk' => 0.9,
                    'expectedConsumption' => 5400.0,
                    'remainingBudget' => 6000.0,
                ],
            ],
            'fridayZeroConsumptionFasting' => [
                'currentDay' => '5',
                'weeklyGoal' => 14000.0,
                'weeklyConsumption' => 0.0,
                'todayConsumption' => 0.0,
                'expectedOutput' => [
                    'risk' => 0.0,
                    'expectedConsumption' => 0.0,
                    'remainingBudget' => 14000.0,
                ],
            ],
            'sundayLastDayOfWeek' => [
                'currentDay' => '7',
                'weeklyGoal' => 14000.0,
                'weeklyConsumption' => 12000.0,
                'todayConsumption' => 1000.0,
                'expectedOutput' => [
                    'risk' => 0.0,
                    'expectedConsumption' => 0.0,
                    'remainingBudget' => 1000.0,
                ],
            ],
            'budgetDepletedExactZero' => [
                'currentDay' => '3',
                'weeklyGoal' => 14000.0,
                'weeklyConsumption' => 14000.0,
                'todayConsumption' => 0.0,
                'expectedOutput' => [
                    'risk' => 1.3,
                    'expectedConsumption' => 35000.0,
                    'remainingBudget' => 0.0,
                ],
            ],
            'budgetExceededNegativeRemaining' => [
                'currentDay' => '4',
                'weeklyGoal' => 14000.0,
                'weeklyConsumption' => 15000.0,
                'todayConsumption' => 500.0,
                'expectedOutput' => [
                    'risk' => 1.3,
                    'expectedConsumption' => 19500.0,
                    'remainingBudget' => -1500.0,
                ],
            ],
        ];
    }
}
