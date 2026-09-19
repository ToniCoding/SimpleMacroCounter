<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use App\DTO\MacroDataDTO;
use App\Entity\User;
use App\Exceptions\ExceededMacroLimitException;
use App\Repository\KcalsDailyRepository;

/**
 * Service responsible for validating, calculating calories for, and updating 
 * a user's daily macronutrient intake records.
 */
class MacroIntakeUpdater {
    
    /**
     * Initializes the service with the required repository, intake record service, and logger.
     * 
     * @param KcalsDailyRepository $kcalsDailyRepository Repository for managing daily kcal and macro entities.
     * @param DailyIntakeRecordService $dailyIntakeRecordService Service to ensure and manage daily intake records.
     * @param LoggerInterface $logger Logger service to record actions and errors.
     */
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private DailyIntakeRecordService $dailyIntakeRecordService,
        private LoggerInterface $logger
        ) {}

    /**
     * Validates macronutrient limits, computes total calories, and updates the user's daily intake.
     * 
     * @param User $user The user whose macro intake is being updated.
     * @param MacroDataDTO $macroDataDTO The DTO containing the macronutrient values and intent.
     * @param string $intent The operation intent (e.g., 'add' or 'reduce').
     * @return bool Returns true on successful update, false otherwise.
     * @throws ExceededMacroLimitException If macro limits are exceeded or reduction rules are violated.
     */
    public function updateMacroIntake(User $user, MacroDataDTO $macroDataDTO, string $intent = 'add'): bool {
        $dataProtein = (float) $macroDataDTO->getProtein();
        $dataCarbs = (float) $macroDataDTO->getCarbs();
        $dataFats = (float) $macroDataDTO->getFats();
        $dataFiber = (float) $macroDataDTO->getFiber();

        $dataMacros = [$dataProtein, $dataCarbs, $dataFats, $dataFiber];

        $currentMacros = $this->dailyIntakeRecordService->ensureDailyIntakeRecord($user);

        $dataMacrosConsumedAsArray = [
            (float) $currentMacros->getProtein(),
            (float) $currentMacros->getCarbs(),
            (float) $currentMacros->getFats(),
            (float) $currentMacros->getFiber()
        ];

        if (array_any($dataMacros, fn($v) => (float) $v > 400)) {
            $this->logger->error("[MACRO_INTAKE_UPDATER_SERVICE] User tried to $intent more than 400 grams for the macro.");

            throw new ExceededMacroLimitException(
                "You cannot $intent more than 400 of one macro-nutrient in one intake."
            );
        }

        if ($intent === 'reduce') {
            foreach ($dataMacros as $ind => $macro) {
                if ($macro > $dataMacrosConsumedAsArray[$ind]) {
                    $this->logger->error("[MACRO_INTAKE_UPDATER_SERVICE] User tried to reduce $macro but it had " .  $dataMacrosConsumedAsArray[$ind]);
                    
                    throw new ExceededMacroLimitException(
                        'You cannot reduce more than you have consumed.'
                    );
                }
            }
        }

        $macroDataDTO->setCalories(
            $dataProtein * 4 +
            $dataFats * 9 +
            $dataCarbs * 4 +
            $dataFiber * 2
        );

        $this->logger->info('[MACRO_INTAKE_UPDATER_SERVICE] Macro data to use: ' . $macroDataDTO->__toString());

        return $this->kcalsDailyRepository->updateMacroIntake(
            $user,
            $macroDataDTO,
            $intent
        );
    }
}
