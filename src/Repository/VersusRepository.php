<?php

namespace App\Repository;

use App\Entity\Versus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Versus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Versus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Versus[]    findAll()
 * @method Versus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Versus::class);
    }

    public function verification_versus($city1, $city2){
        $builder = $this->createQueryBuilder('v')
        ->where('v.city1 = :city_name1 and v.city2 = :city_name2 or v.city1 = :city_name2 and v.city2 = :city_name1')
        ->setParameters(array("city_name1" => $city1, "city_name2" => $city2));
        return ($builder->getQuery()->execute());
    }

    // /**
    //  * @return Versus[] Returns an array of Versus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Versus
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
