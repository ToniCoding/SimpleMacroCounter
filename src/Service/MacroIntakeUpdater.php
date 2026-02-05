<?php

namespace src\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use src\DTO\MacroDataDTO;
use src\Entity\User;
use src\Exceptions\ExceededMacroLimitException;
use src\Repository\KcalsDailyRepository;

class MacroIntakeUpdater extends ServiceEntityRepository {
    public function __construct(private KcalsDailyRepository $kcalsDailyRepository) {}

    public function updateMacroIntake(User $user, MacroDataDTO $macroDataDTO, string $intent = 'add'): bool {
        $dataProtein = $macroDataDTO->getProtein();
        $dataCarbs = $macroDataDTO->getCarbs();
        $dataFats = $macroDataDTO->getFats();
        $dataFiber = $macroDataDTO->getFiber();

        $dataMacros = [$dataProtein, $dataCarbs, $dataFats, $dataFiber];

        if (array_any($dataMacros, fn($v) => $v > 400)) {
            throw new ExceededMacroLimitException("You cannot $intent more than 400 of one macro-nutrient in one intake.");
        }

        $macroDataDTO->setCalories(
                $dataProtein * 4 +
                $dataFats * 9 +
                $dataCarbs * 4 +
                $dataFiber * 2
            );

        return $this->kcalsDailyRepository->updateMacroIntake($user, $macroDataDTO, $intent);
    }
}
