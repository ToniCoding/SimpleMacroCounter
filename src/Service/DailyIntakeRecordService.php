<?php

namespace App\Service;

use App\DTO\{DailyIntakeDTO, MacroSettingsDTO, UserGoalsDTO};
use App\Entity\{KcalsDaily, User, UserGoals};
use App\Exceptions\WriteToDatabaseException;
use App\Repository\{KcalsDailyRepository, UserGoalsRepository};
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service responsible for ensuring and managing daily intake records and user macro goals.
 * 
 * Handles retrieval, creation, and update of KcalsDaily and UserGoals entities,
 * using DTOs for data transfer and applying minimum value constraints.
 */
class DailyIntakeRecordService {
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserGoalsRepository $userGoalsRepository,
        private ParameterBagInterface $params,
        private LoggerInterface $log
    ) {}

    /**
     * Once called, ensures there is one intake registry for the user.
     * @param User $user: The user to check.
     * @throws WriteToDatabaseException: If there is any error during the registry insertion.
     * @return DailyIntakeDTO: The DTO with the existing or created data.
     */
    public function ensureDailyIntakeRecord(User $user): DailyIntakeDTO {
        $this->log->info('[DailyIntakeRecordService] Start looking for a intake record for user.' . $user->getId() . '.');
        $intakeRecordExist = $this->kcalsDailyRepository->findIntakeRegistryForToday($user);
        
        if ($intakeRecordExist) {
            $this->log->info('[DailyIntakeRecordService] Found existing record.');
            return $this->kcalsDailyEntityToDto($intakeRecordExist);
        };

        $newkcalsDailyRecord = $this->createKcalsDailyEntity($user);
        $kcalsDailyEntityPersist = $this->kcalsDailyRepository->insertIntakeRegistry($newkcalsDailyRecord);

        if (!$kcalsDailyEntityPersist) throw new WriteToDatabaseException('There was an error writing to database.');

        $this->log->info('[DailyIntakeRecordService] New daily intake record created and saved.');
        return $this->kcalsDailyEntityToDto($newkcalsDailyRecord);
    }

    /**
     * Once called, ensures there is one intake goal registry for the user.
     * @param User $user The user to check.
     * @throws WriteToDatabaseException If there is any error during the registry insertion.
     * @return UserGoalsDTO The DTO with the existing or created data.
     */
    public function ensureOneMacroGoal(User $user): UserGoalsDTO {
        $this->log->info('[DailyIntakeRecordService] Start looking for a intake goal record ' . $user->getId() . '.');
        $intakeGoalRecordExist = $this->userGoalsRepository->findOneBy(['user' => $user]);

        if ($intakeGoalRecordExist) {
            $this->log->info('[DailyIntakeRecordService] Found existing record.');
            return $this->userGoalsEntityToDto($intakeGoalRecordExist);
        }

        $newIntakeRecord = $this->createUserGoalEntity($user);
        $newIntakeRecordPersist = $this->userGoalsRepository->insertGoalRegistry($newIntakeRecord);

        if (!$newIntakeRecordPersist) throw new WriteToDatabaseException('There was an error writing to database.');
    
        $this->log->info('[DailyIntakeRecordService] New user intake goal created and saved.');
        return $this->userGoalsEntityToDto($newIntakeRecord);
    }

    /**
     * Modifies the goals for the user, filters out the zero values ignoring them and sets minimum
     * values if the user tries setting low values.
     * @param User $user: Owner of the registry.
     * @param MacroSettingsDTO $macroSettingsDTO: The new setting values.
     * @return bool: True on success, false otherwise. 
     */
    public function modifyMacroGoal(User $user, MacroSettingsDTO $macroSettingsDTO): bool {
        $macroSettingsArray = $macroSettingsDTO->__toArray();
        $minMacroSettings = $this->getMinimumMacroValues();

        $this->log->info('[DailyIntakeRecordService] Checking values for new settings: ' . $macroSettingsDTO);

        foreach ($macroSettingsArray as $macroName => $macroValue) {
            if ($macroValue === 0) continue;

            if ($macroValue < $minMacroSettings[$macroName]) {
                $macroSettingsArray[$macroName] = $minMacroSettings[$macroName];
            }
        }

        $this->log->info('[DailyIntakeRecordService] New settings validated and filters applied: ' . json_encode($macroSettingsDTO));
        return $this->userGoalsRepository->updateGoalRegistry($user, $macroSettingsArray);
    }

    /**
     * Creates a new KcalsDaily entity with default values.
     * @param User $user: Owner of the new registry.
     * @return KcalsDaily: The entity to create.
     */
    private function createKcalsDailyEntity(User $user): KcalsDaily {
        return new KcalsDaily(user: $user);
    }

    /**
     * Creates a new UserGoals entity with default parameter values.
     * @param User $user: Owner of the new registry.
     * @return UserGoals: The entity to create.
     */
    private function createUserGoalEntity(User $user): UserGoals {
        return new UserGoals(
            user: $user,
            calories: $this->params->get('nutrition.default_calories'),
            protein: $this->params->get('nutrition.default_protein'),
            carbs: $this->params->get('nutrition.default_carb'),
            fats: $this->params->get('nutrition.default_fat'),
            fiber: $this->params->get('nutrition.default_fiber')
        );
    }

    /**
     * Converts a KcalsDaily entity to a DTO.
     * @param KcalsDaily $kcalsDailyEntity: The entity to convert.
     * @return DailyIntakeDTO: The entity converted into a DTO.
     */
    private function kcalsDailyEntityToDto(KcalsDaily $kcalsDailyEntity): DailyIntakeDTO {
        return new DailyIntakeDTO(
            (float) $kcalsDailyEntity->getKcals(),
            (float) $kcalsDailyEntity->getProtein(),
            (float) $kcalsDailyEntity->getCarbs(),
            (float) $kcalsDailyEntity->getFats(),
            (float) $kcalsDailyEntity->getFiber()
        );
    }

    /**
     * Converts a UserGoals entity to a DTO.
     * @param UserGoals $userGoalsEntity: The entity to convert.
     * @return UserGoalsDTO: The entity converted into a DTO.
     */
    private function userGoalsEntityToDto(UserGoals $userGoalsEntity): UserGoalsDTO {
        return new UserGoalsDTO(
            $userGoalsEntity->getCalories(),
            $userGoalsEntity->getProtein(),
            $userGoalsEntity->getCarbs(),
            $userGoalsEntity->getFats(),
            $userGoalsEntity->getFiber()
        );
    }

    /**
     * Gets the minimum value for each macro-nutrient and calorie intake. The values come from the ParameterBagInterface
     * defined in the `services.yaml` file.
     * @return array{calories: int, carbs: int, fats: int, fiber: int, protein: int}
     */
    private function getMinimumMacroValues(): array {
        return [
            'calories' => $this->params->get('nutrition.minimum_calories'),
            'protein' => $this->params->get('nutrition.minimum_protein'),
            'carbs' => $this->params->get('nutrition.minimum_carb'),
            'fats' => $this->params->get('nutrition.minimum_fat'),
            'fiber' => $this->params->get('nutrition.minimum_fiber'),
        ];
    }
}
