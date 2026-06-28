<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\{AllowedMarkets, Food};
use App\Exceptions\{FoodAlreadyRegistered, FoodNotFound, MarketNotAllowed};

class FoodsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Food::class);
    }

    public function getFood(int $foodId): ?Food {
        return $this->createQueryBuilder('f')
            ->where('f.id = :foodId')
            ->setParameter('foodId', $foodId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getFoodsByMarket(string $market, int $offset): array {
        if (!empty($market)) {
            return $this->createQueryBuilder('f')
                -> where('f.market = :market')
                -> setParameter('market', $market)
                -> setMaxResults(100)
                -> setFirstResult($offset)
                -> getQuery()
                -> getResult();
        }

        return $this->createQueryBuilder('f')
        ->getQuery()
        -> setMaxResults(100)
        -> setFirstResult($offset)
        ->getResult();
    }

    public function registerFood(Food $food): void {
        if ($this->checkIfFoodIsDuplicated($food)) throw new FoodAlreadyRegistered();

        $this->getEntityManager()->persist($food);
        $this->getEntityManager()->flush();
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
        $this->getEntityManager()->flush();

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
        if ($this->getEntityManager()->getRepository(AllowedMarkets::class)->findOneBy(['name' => $marketName])) return true;

        return false;
    }
}
