<?php

namespace App\Repository;

use App\Entity\Housing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Housing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Housing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Housing[]    findAll()
 * @method Housing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HousingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Housing::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Housing $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Housing $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findBySearch($array)
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.bookings', 'b');
            if($array['city']) {
                $qb->andWhere('h.city = :city')
                    ->setParameter('city', $array['city']);
            }
            if($array['category']) {
                $qb->andWhere('h.category = :category')
                    ->setParameter('category', $array['category']);
            }
            if($array['availablePlaces']) {
                $qb->andWhere('h.availablePlaces >= :availablePlaces')
                    ->setParameter('availablePlaces', $array['availablePlaces']);
            }
            if($array['dailyPrice']) {
                $qb->andWhere('h.dailyPrice <= :dailyPrice')
                    ->setParameter('dailyPrice', $array['dailyPrice']);
            }
            if($array['exitDate'] > $array['entryDate']) {
                if($array['entryDate']) {
                    $qb->andWhere('b.exitDate < :entryDate OR b.exitDate IS NULL')
                        ->setParameter('entryDate', $array['entryDate']);
                }
                if($array['exitDate']) {
                    $qb->andWhere('b.entryDate > :exitDate OR b.entryDate IS NULL')
                        ->setParameter('exitDate', $array['exitDate']);
                }
            }
        return $qb->getQuery()
                  ->getResult();
    }

    // /**
    //  * @return Housing[] Returns an array of Housing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Housing
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
