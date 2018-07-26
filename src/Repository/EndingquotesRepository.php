<?php

namespace App\Repository;

use App\Entity\Endingquotes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Endingquotes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Endingquotes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Endingquotes[]    findAll()
 * @method Endingquotes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EndingquotesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Endingquotes::class);
    }

//    /**
//     * @return Endingquotes[] Returns an array of Endingquotes objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Endingquotes
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
