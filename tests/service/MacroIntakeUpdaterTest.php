<?php

namespace Smc\Tests\Service;

use App\DTO\DailyIntakeDTO;
use App\Service\DailyIntakeRecordService;
use App\Service\MacroIntakeUpdater;
use Psr\Log\LoggerInterface;
use App\DTO\MacroDataDTO;
use App\Entity\User;
use App\Exceptions\ExceededMacroLimitException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use App\Repository\KcalsDailyRepository;

class MacroIntakeUpdaterTest extends TestCase {
    private $user;
    private $kcalsDailyRepository;
    private $dailyIntakeRecordService;
    private $loggerInterface;
    private MacroIntakeUpdater $service;

    public function setUp(): void {
        $this->user = $this->createMock(User::class);
        $this->kcalsDailyRepository = $this->createMock(KcalsDailyRepository::class);
        $this->dailyIntakeRecordService = $this->createMock(DailyIntakeRecordService::class);
        $this->loggerInterface = $this->createMock(LoggerInterface::class);
        $this->service = new MacroIntakeUpdater(
            $this->kcalsDailyRepository,
            $this->dailyIntakeRecordService,
            $this->loggerInterface
        );
    }

    /**
     ********************************************************
     *** Tests for updateMacroIntake method ***********
     ********************************************************
     */

    /**
     * Tests for adding the consumed macro-nutrients and calculating the new amount of calories.
     * @return void  
    */
    #[DataProvider('macroIntakeAddProvider')]
    public function testUpdateMacroIntakeAdd (
        MacroDataDTO $macroDataDto,
        DailyIntakeDTO $currentIntake,
        float $expectedCalories
    ): void {
        $this->loggerInterface
            -> expects($this->exactly(1))
            -> method('info');
        
        $this->dailyIntakeRecordService
            -> expects($this->once())
            -> method('ensureDailyIntakeRecord')
            -> willReturn($currentIntake);

        $this->kcalsDailyRepository
            -> expects($this->once())
            -> method('updateMacroIntake')
            -> with($this->user, $macroDataDto, 'add')
            -> willReturn(true);

        $testedMethodResult = $this->service->updateMacroIntake(
            $this->user,
            $macroDataDto,
            'add'
        );

        $this->assertTrue($testedMethodResult);
        $this->assertSame($expectedCalories, $macroDataDto->getCalories());
    }

    /**
     * Tests for reducing the consumed macro-nutrients and calculating the new amount of calories.
     * @return void 
    */
    #[DataProvider('macroIntakeReduceProvider')]
    public function testUpdateMacroIntakeReduce (
        MacroDataDTO $macroDataDto,
        DailyIntakeDTO $currentIntake,
        float $expectedCalories
    ): void {
        $this->loggerInterface
            -> expects($this->exactly(1))
            -> method('info');
        
        $this->dailyIntakeRecordService
            -> expects($this->once())
            -> method('ensureDailyIntakeRecord')
            -> willReturn($currentIntake);

        $this->kcalsDailyRepository
            -> expects($this->once())
            -> method('updateMacroIntake')
            -> with($this->user, $macroDataDto, 'reduce')
            -> willReturn(true);

        $testedMethodResult = $this->service->updateMacroIntake(
            $this->user,
            $macroDataDto,
            'reduce'
        );

        $this->assertTrue($testedMethodResult);
        $this->assertSame($expectedCalories, $macroDataDto->getCalories());
    }

    /**
     * Test for adding and reducing more than 400 grams for any macro-nutrient.
     * @return void
     */
    #[DataProvider('exceededMacroLimitExceptionCasesProvider')]
    public function testUpdateMacroIntakeAddExceededMacroLimitException(MacroDataDTO $macroDataDto): void {
        $this->expectException(ExceededMacroLimitException::class);

        $currentIntake = new DailyIntakeDTO(0, 0, 0, 0, 0);

        $this->loggerInterface
            ->expects($this->once())
            ->method('error');
        
        $this->dailyIntakeRecordService
            ->expects($this->once())
            ->method('ensureDailyIntakeRecord')
            ->willReturn($currentIntake);

        $this->service->updateMacroIntake(
            $this->user,
            $macroDataDto,
            'add'
        );
    }

    #[DataProvider('exceededReduceLimitCasesProvider')]
    public function testUpdateMacroIntakeReduceExceededLimitException(
        MacroDataDTO $macroDataDto,
        DailyIntakeDTO $currentIntake
    ): void {
        $this->expectException(ExceededMacroLimitException::class);

        $this->loggerInterface
            ->expects($this->once())
            ->method('error');

        $this->dailyIntakeRecordService
            ->expects($this->once())
            ->method('ensureDailyIntakeRecord')
            ->willReturn($currentIntake);

        $this->service->updateMacroIntake(
            $this->user,
            $macroDataDto,
            'reduce'
        );
    }

    /**
     ********************************************************
     *** DATA PROVIDERS *************************************
     ********************************************************
     */
    public static function macroIntakeAddProvider(): array {
        return [
            'happyPath' => [
                'macroDataDto' => new MacroDataDTO(0, 0, 0, 0),
                'currentIntake' => new DailyIntakeDTO(0, 0, 0, 0, 0),
                'expectedCalories' => 0.0
            ],
            'nonZeroValues' => [
                'macroDataDto' => new MacroDataDTO(15, 15, 15, 15),
                'currentIntake' => new DailyIntakeDTO(285, 15, 15, 15, 15),
                'expectedCalories' => 285.0
            ],
            'noCurrentIntake' => [
                'macroDataDto' => new MacroDataDTO(15, 15, 15, 15),
                'currentIntake' => new DailyIntakeDTO(0, 0, 0, 0, 0),
                'expectedCalories' => 285.0
            ],
            'noMacroUpdate' => [
                'macroDataDto' => new MacroDataDTO(0, 0, 0, 0),
                'currentIntake' => new DailyIntakeDTO(285, 15, 15, 15, 15),
                'expectedCalories' => 0.0
            ],
        ];
    }

    public static function macroIntakeReduceProvider(): array {
        return [
            'happyPath' => [
                'macroDataDto' => new MacroDataDTO(0, 0, 0, 0),
                'currentIntake' => new DailyIntakeDTO(0, 0, 0, 0, 0),
                'expectedCalories' => 0.0
            ],
            'nonZeroValues' => [
                'macroDataDto' => new MacroDataDTO(15, 15, 15, 15),
                'currentIntake' => new DailyIntakeDTO(285, 15, 15, 15, 15),
                'expectedCalories' => 285.0
            ],
            'noMacroUpdate' => [
                'macroDataDto' => new MacroDataDTO(0, 0, 0, 0),
                'currentIntake' => new DailyIntakeDTO(285, 15, 15, 15, 15),
                'expectedCalories' => 0.0
            ],
        ];
    }

    public static function exceededMacroLimitExceptionCasesProvider(): array {
        return [
            'proteinIsAboveLimitAdd'    => [new MacroDataDTO(401, 0, 0, 0, 0, 'add')],
            'carbsAreAboveLimitAdd'     => [new MacroDataDTO(0, 401, 0, 0, 0, 'add')],
            'fatsAreAboveLimitAdd'      => [new MacroDataDTO(0, 0, 401, 0, 0, 'add')],
            'fiberIsAboveLimitAdd'      => [new MacroDataDTO(0, 0, 0, 401, 0, 'add')],
            'proteinIsAboveLimitReduce' => [new MacroDataDTO(401, 0, 0, 0, 0, 'reduce')],
            'carbsAreAboveLimitReduce'  => [new MacroDataDTO(0, 401, 0, 0, 0, 'reduce')],
            'fatsAreAboveLimitReduce'   => [new MacroDataDTO(0, 0, 401, 0, 0, 'reduce')],
            'fiberIsAboveLimitReduce'   => [new MacroDataDTO(0, 0, 0, 401, 0, 'reduce')],
        ];
    }

    public static function exceededReduceLimitCasesProvider(): array {
        $consumedIntake = new DailyIntakeDTO(190, 10, 10, 10, 10);

        return [
            'reduceProteinExceeded' => [
                'macroDataDto' => new MacroDataDTO(11, 0, 0, 0, 0, 'reduce'),
                'currentIntake' => $consumedIntake,
            ],
            'reduceCarbsExceeded' => [
                'macroDataDto' => new MacroDataDTO(0, 11, 0, 0, 0, 'reduce'),
                'currentIntake' => $consumedIntake,
            ],
            'reduceFatsExceeded' => [
                'macroDataDto' => new MacroDataDTO(0, 0, 11, 0, 0, 'reduce'),
                'currentIntake' => $consumedIntake,
            ],
            'reduceFiberExceeded' => [
                'macroDataDto' => new MacroDataDTO(0, 0, 0, 11, 0, 'reduce'),
                'currentIntake' => $consumedIntake,
            ],
        ];
    }
}
