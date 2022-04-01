<?php

namespace App\Repository;

use App\Entity\Housing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
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


     /**
      * @return Housing[] Returns an array of Housing objects
      */
    public function findBySearch($data): array
    {
        $entryDate = $data['entryDate'];
        $exitDate = $data['exitDate'];
        $nbGuest = $data['nbGuest'];
        $category = $data['category'];
        $localisation = $data['localisation'];

        $qb =$this->createQueryBuilder('h')
        ->leftJoin('h.bookings', 'b');
        if ($entryDate) {
            $qb->andWhere('b.exitDate > :entryDate OR b.exitDate is null')
                ->setParameter('entryDate', $entryDate);
        }
        if ($exitDate) {
            $qb->andWhere('b.entryDate > :exitDate OR b.entryDate is null')
                ->setParameter('exitDate', $exitDate);
        }if ($nbGuest) {
            $qb->andWhere('h.availablePlaces >= :nbGuest')
//                $qb->join('h.rooms', 'r')
//                    ->join('r.bedRooms', 'br')
                   ->setParameter('nbGuest', $nbGuest);
        }
        if ($category) {
            $qb->andWhere('h.category = :category')
                ->setParameter('category', $category);
        }
        if ($localisation) {
            $qb->andWhere('h.postalCode LIKE :localisation')
                ->setParameter('localisation', '%'. $localisation .'%');
        }

        $query = $qb->getQuery();
        return $query->execute();

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
