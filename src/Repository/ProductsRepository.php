<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use src\Entity\Products;

class ProductsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Products::class);
    }

    public function getProductsByMarket(string $market, int $offset, int $limit = 125): array {
        $qb = $this->createQueryBuilder('p');

        if (!empty($market) && $market !== 'all') {
            $qb->where('p.market = :market')
               ->setParameter('market', $market);
        }

        $totalQb = clone $qb;
        $total = $totalQb->select('COUNT(p.id)')
                         ->getQuery()
                         ->getSingleScalarResult();

        $results = $qb->setMaxResults($limit)
                      ->setFirstResult($offset)
                      ->getQuery()
                      ->getResult();

        return [
            'data' => $results,
            'total' => (int) $total,
            'limit' => $limit,
            'offset' => $offset,
            'currentPage' => ($offset / $limit) + 1,
            'totalPages' => ceil($total / $limit)
        ];
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

    public function fullTextSearch(string $search, int $offset, int $limit = 125): array {
        $search = trim($search);
        if (empty($search)) {
            return ['data' => [], 'total' => 0, 'totalPages' => 0];
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

        $countSql = '
            SELECT COUNT(*) as total
            FROM products p
            WHERE MATCH(p.product_name, p.market, p.brand) AGAINST(:search IN BOOLEAN MODE) > 0
        ';
        $countStmt = $conn->prepare($countSql);
        $countStmt->bindValue('search', $searchStr);
        $total = (int) $countStmt->executeQuery()->fetchOne();

        $sql = '
            SELECT 
                p.id,
                p.product_name,
                p.market,
                p.brand,
                p.kcal,
                p.protein,
                p.carbs,
                p.fats,
                p.fiber,
                MATCH(p.product_name, p.market, p.brand) AGAINST(:search) AS relevance
            FROM products p
            WHERE MATCH(p.product_name, p.market, p.brand) AGAINST(:search IN BOOLEAN MODE) > 0
            ORDER BY relevance DESC
            LIMIT :limit OFFSET :offset
        ';

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('search', $searchStr);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $result = $stmt->executeQuery();

        $rawResults = $result->fetchAllAssociative();
        $products = [];

        foreach ($rawResults as $row) {
            $product = $this->find($row['id']);
            if ($product) {
                $products[] = $product;
            }
        }

        return [
            'data' => $products,
            'total' => $total,
            'totalPages' => ceil($total / $limit)
        ];
    }

    public function searchByFullText(string $search, int $limit = 20): array {
        return $this->fullTextSearch($search, $limit);
    }
}