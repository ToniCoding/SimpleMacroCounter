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

    public function getProductById(int $id) {}

    public function fullTextSearch(string $search, int $limit = 20): array {
        $search = trim($search);
        if (empty($search)) {
            return [];
        }

        $terms = explode(' ', $search);
        $searchTerms = array_map(function($term) {
            $term = trim($term);
            if (strlen($term) < 2) {
                return $term;
            }
            return '+' . $term . '*';
        }, $terms);
        $searchStr = implode(' ', $searchTerms);

        $conn = $this->getEntityManager()->getConnection();
        
        $sql = '
            SELECT 
                p.id,
                p.product_name,
                p.market,
                p.kcal,
                p.protein,
                p.carbs,
                p.fats,
                p.fiber,
                MATCH(p.product_name, p.market) AGAINST(:search) AS relevance
            FROM products p
            WHERE MATCH(p.product_name, p.market) AGAINST(:search IN BOOLEAN MODE) > 0
            ORDER BY relevance DESC
            LIMIT :limit
        ';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('search', $searchStr);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $result = $stmt->executeQuery();
        
        $rawResults = $result->fetchAllAssociative();
        $products = [];
        
        foreach ($rawResults as $row) {
            $product = $this->find($row['id']);
            if ($product) {
                $products[] = $product;
            }
        }
        
        return $products;
    }

    public function searchByFullText(string $search, int $limit = 20): array {
        return $this->fullTextSearch($search, $limit);
    }
}