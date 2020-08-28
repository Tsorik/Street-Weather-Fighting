<?php

namespace App\Repository;

use App\Entity\VillesFranceFree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;


/**
 * @method VillesFranceFree|null find($id, $lockMode = null, $lockVersion = null)
 * @method VillesFranceFree|null findOneBy(array $criteria, array $orderBy = null)
 * @method VillesFranceFree[]    findAll()
 * @method VillesFranceFree[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VillesFranceFreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VillesFranceFree::class);
    }

   /**
    * @return VillesFranceFree[] Returns an array of Cities objects
    */
    public function findByName($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.villeNom like :villeNom')
            ->setParameter('villeNom', "%". $value ."%")
            // ->orderBy('c.id', 'ASC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    // public function __construct(ManagerRegistry $registry)
    // {
    //     parent::__construct($registry, VillesFranceFree::class);
    // }


    // /**
    //  * @return VillesFranceFree[]
    //  */
    // public function findAllVisible(): array
    // {
    //     return $this->findVisibleQuery()
    //         ->getQuery()
    //         ->getResult();
    // }

    // /**
    //  * @return VillesFranceFree[]
    //  */
    // public function findLatest(): array
    // {
    //     return $this->findVisibleQuery()
    //         ->setMaxResults(4)
    //         ->getQuery()
    //         ->getResult();
    //  }

// private functions

/*private function findVisibleQuery(): QueryBuilder
{
    return $this->createQueryBuilder('p')
    ->where('p.sold = false');
}*/

    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
