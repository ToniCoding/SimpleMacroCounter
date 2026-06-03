<?php

namespace src\Service;

use Psr\Log\LoggerInterface;
use src\DTO\MacroSettingsDTO;
use src\Exceptions\UnrecognizedMacroException;
use src\Exceptions\WriteToDatabaseException;
use src\Repository\{UserGoalsRepository, KcalsDailyRepository};
use src\Entity\{KcalsDaily, User, UserGoals};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Sub-service with the methods needed to check if there is any registry for user intake and goals.
 */
class DailyIntakeRecord {
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserGoalsRepository $userGoalsRepository,
        private ParameterBagInterface $params,
        private LoggerInterface $logger
    ) {}

    /**
     * Checks if there is an intake registry for today. If none, a new registry is created.
     * 
     * @param User $user Find the specific registry for the user.
     * @throws WriteToDatabaseException If there is any error while creating the intake registry.
     * @return KcalsDaily Existing registry for the day.
     */
    public function ensureDailyIntakeRecord(User $user): KcalsDaily {
        $existingMacroRecord = $this->kcalsDailyRepository->findIntakeRegistryForToday($user);

        if ($existingMacroRecord) return $existingMacroRecord;

        $this->logger->info('[DAILY_INTAKE_RECORD_SERVICE] Macro record for user not found. Registering one.');

        $newMacroRecord = new KcalsDaily($user);

        $newMacroRecord->setKcals(0);
        $newMacroRecord->setProtein("0.00");
        $newMacroRecord->setCarbs("0.00");
        $newMacroRecord->setFats("0.00");
        $newMacroRecord->setFiber("0.00");

        if (!$this->kcalsDailyRepository->insertIntakeRegistry($newMacroRecord)) {
            $this->logger->error('[DAILY_INTAKE_RECORD_SERVICE] There was an error inserting the new intake.');
            throw new WriteToDatabaseException('There was an error inserting a new intake registry.');
        }

        $this->kcalsDailyRepository->insertIntakeRegistry($newMacroRecord);

        $this->logger->error('[DAILY_INTAKE_RECORD_SERVICE] Successfully registered the intake.');

        return $this->kcalsDailyRepository->findIntakeRegistryForToday($user);
    }

    /**
     * Checks if there is an intake goal registry for the user. If none, a new registry is created.
     * @param User $user Find the specific registry for the user.
     * @throws WriteToDatabaseException If there is any error while creating the intake registry.
     * @return UserGoals Existing registry for the user.
     */
    public function ensureOneMacroGoal(User $user): UserGoals {
        $existingMacroRecord = $this->userGoalsRepository->findGoalsRegistry($user);

        if ($existingMacroRecord) return $existingMacroRecord;

        $this->logger->info('[DAILY_INTAKE_RECORD_SERVICE] Macro record goal for user not found. Registering one.');

        $newGoalRegistry = new UserGoals($user, new \DateTime());

        $newGoalRegistry->setCalories($this->params->get('nutrition.default_calories'));
        $newGoalRegistry->setProtein($this->params->get('nutrition.default_protein'));
        $newGoalRegistry->setCarbs($this->params->get('nutrition.default_carb'));
        $newGoalRegistry->setFats($this->params->get('nutrition.default_fat'));
        $newGoalRegistry->setFiber($this->params->get('nutrition.default_fiber'));

        $this->userGoalsRepository->insertGoalRegistry($newGoalRegistry);

        $this->logger->info('[DAILY_INTAKE_RECORD_SERVICE] Successfully registered the goals.');

        return $this->userGoalsRepository->findGoalsRegistry($user);
    }

    public function modifyMacroGoal(User $user, MacroSettingsDTO $macroSettingsDTO): void {
        $validatedMacroData = [];

        $macroUpdates = [
            'calories' => $macroSettingsDTO->getNewCalories(),
            'protein' => $macroSettingsDTO->getNewProtein(),
            'carbs' => $macroSettingsDTO->getNewCarbs(),
            'fats' => $macroSettingsDTO->getNewFats(),
            'fiber' => $macroSettingsDTO->getNewFiber()
        ];

        foreach ($macroUpdates as $macroName => $macroValue) {

            if ($macroValue === 0) continue;

            $macroMinimum = $this->getMinimumMacroValue($macroName);

            if ($macroMinimum === null) {
                $this->logger->warning('[DAILY_INTAKE_RECORD_SERVICE] Attempt to register unallowed macro.');
                throw new UnrecognizedMacroException();
            }

            if ($macroValue <= $macroMinimum) {
                $validatedMacroData[$macroName] = ($macroName === 'calories')
                    ? (int) $macroMinimum
                    : (string) $macroMinimum;
            } else {
                $validatedMacroData[$macroName] = ($macroName === 'calories')
                    ? (int) $macroValue
                    : (string) $macroValue;
            }
        }

        if (empty($validatedMacroData)) return;

        $this->logger->info('[DAILY_INTAKE_RECORD_SERVICE] Validated macro goal is: '. json_encode($validatedMacroData));

        $this->userGoalsRepository->updateGoalRegistry($user, $validatedMacroData);
    }

    private function getMinimumMacroValue(string $macro): ?int {
        return match($macro) {
            'calories' => $this->params->get('nutrition.minimum_calories'),
            'protein' => $this->params->get('nutrition.minimum_protein'),
            'carbs' => $this->params->get('nutrition.minimum_carb'),
            'fats' => $this->params->get('nutrition.minimum_fat'),
            'fiber' => $this->params->get('nutrition.minimum_fiber'),
            default => null
        };
    }
}
