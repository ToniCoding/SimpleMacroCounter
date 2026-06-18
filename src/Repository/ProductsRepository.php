<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use src\Entity\Products;

class ProductsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Products::class);
    }

    public function getProductsByMarket(string $market, int $offset): array {
        $qb = $this->createQueryBuilder('p');
        
        if (!empty($market)) {
            $qb->where('p.market = :market')
               ->setParameter('market', $market);
        }
        
        return $qb->setMaxResults(100)
                  ->setFirstResult($offset)
                  ->getQuery()
                  ->getResult();
    }

    public function searchProducts(string $search, int $offset): array {
        return $this->createQueryBuilder('p')
            ->where('p.product_name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setMaxResults(100)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}