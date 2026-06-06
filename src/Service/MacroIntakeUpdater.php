<?php

namespace src\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Psr\Log\LoggerInterface;
use src\DTO\MacroDataDTO;
use src\Entity\User;
use src\Exceptions\ExceededMacroLimitException;
use src\Repository\KcalsDailyRepository;

class MacroIntakeUpdater {
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserMacrosRetrieve $userMacrosRetrieve,
        private LoggerInterface $logger
        ) {}

    public function updateMacroIntake(User $user, MacroDataDTO $macroDataDTO, string $intent = 'add'): bool {
        $dataProtein = (float) $macroDataDTO->getProtein();
        $dataCarbs = (float) $macroDataDTO->getCarbs();
        $dataFats = (float) $macroDataDTO->getFats();
        $dataFiber = (float) $macroDataDTO->getFiber();

        $dataMacros = [$dataProtein, $dataCarbs, $dataFats, $dataFiber];

        $currentMacros = $this->userMacrosRetrieve->getConsumedMacros($user);

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
