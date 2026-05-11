<?php

namespace src\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use src\DTO\MacroDataDTO;
use src\Entity\User;
use src\Exceptions\ExceededMacroLimitException;
use src\Repository\KcalsDailyRepository;
use Psr\Log\LoggerInterface;

class MacroIntakeUpdater extends ServiceEntityRepository
{
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserMacrosRetrieve $userMacrosRetrieve,
        private LoggerInterface $log
    ) {
    }

    public function updateMacroIntake(User $user, MacroDataDTO $macroDataDTO, string $intent = 'add'): bool
    {
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
            throw new ExceededMacroLimitException(
                "You cannot $intent more than 400 of one macro-nutrient in one intake."
            );
        }

        if ($intent === 'reduce') {
            foreach ($dataMacros as $ind => $macro) {
                $this->log->error($macro);
                $this->log->error($dataMacrosConsumedAsArray[$ind]);
                $this->log->error($macro < $dataMacrosConsumedAsArray[$ind]);
                if ($macro > $dataMacrosConsumedAsArray[$ind]) {
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

        return $this->kcalsDailyRepository->updateMacroIntake(
            $user,
            $macroDataDTO,
            $intent
        );
    }
}
