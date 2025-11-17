<?php

namespace src\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use src\DTO\MacroDataDTO;
use src\Entity\User;
use src\Repository\KcalsDailyRepository;

class MacroIntakeUpdater extends ServiceEntityRepository {
    public function __construct(private KcalsDailyRepository $kcalsDailyRepository) {}

    public function updateMacroIntake(User $user, MacroDataDTO $macroDataDTO, string $intent = 'add'): bool {
        return $this->kcalsDailyRepository->updateMacroIntake($user, $macroDataDTO, $intent);
    }
}
