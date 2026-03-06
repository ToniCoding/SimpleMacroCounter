<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use src\Entity\{AllowedMarkets, Food};
use src\Exceptions\{FoodAlreadyRegistered, FoodNotFound, MarketNotAllowed};

class FoodsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em) {
        parent::__construct($registry, Food::class);
    }

    public function registerFood(Food $food): void {
        if ($this->checkIfFoodIsDuplicated($food)) throw new FoodAlreadyRegistered();

        $this->em->persist($food);
        $this->em->flush();
    }

    public function updateMarket(int $id, string $market): bool {
        if (!$this->checkIfMarketIsAllowed($market)) {
            throw new MarketNotAllowed();
        }
    
        $foodToMod = $this->find($id);

        if (!$foodToMod) {
            throw new FoodNotFound();
        }

        $foodToMod->setMarket($market);
        $this->em->flush();

        return true;
    }

    private function checkIfFoodIsDuplicated(Food $food): bool {
        $registryFood = $this->findOneBy(['name' => $food->getName()]);

        if ($registryFood && $registryFood->getMarket() == $food->getMarket()) {
            return true;
        }

        return false;
    }

    private function checkIfMarketIsAllowed(string $marketName): bool {
        if ($this->em->getRepository(AllowedMarkets::class)->findOneBy(['name' => $marketName])) return true;

        return false;
    }
}
