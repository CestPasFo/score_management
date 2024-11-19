<?php

namespace App\Repository;

use App\Entity\Equipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipe>
 */
class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

       /**
        * @return Equipe[] Returns an array of Equipe objects
        */
       public function findByExampleField($value): array
       {
           return $this->createQueryBuilder(alias: 'e')
               ->andWhere(where: 'e.exampleField = :val')
               ->setParameter(key: 'val', value: $value)
               ->orderBy(sort: 'e.id', order: 'ASC')
               ->setMaxResults(maxResults: 10)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findOneBySomeField($value): ?Equipe
       {
           return $this->createQueryBuilder(alias: 'e')
               ->andWhere(where: 'e.exampleField = :val')
               ->setParameter(key: 'val', value: $value)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }
}
