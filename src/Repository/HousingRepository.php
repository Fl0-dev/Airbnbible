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

    public function findBySearch($data)
    {
        $conn = $this->getEntityManager()->getConnection();

        $entryDate = $data['entryDate'];
        $exitDate = $data['exitDate'];
        $nbGuest = $data['availablePlaces'];
        $category = $data['category'];
        $city = strtoupper($data['city']);

        $latAndLongQuery = "
        SELECT ville_latitude_deg, ville_longitude_deg FROM spec_villes_france
        WHERE ville_nom = :city
        ";
        $stmt = $conn->prepare($latAndLongQuery);
        $resultSet = $stmt->executeQuery(['city' => $city]);

        $city = $resultSet->fetchAssociative();

        $qb =$this->createQueryBuilder('h')
            ->leftJoin('h.bookings', 'b')
            ->where('h.isVisible = true')
            ->andWhere('h.isDeleted = false');
        $qbDate = $this->createQueryBuilder('h2');
        $qbDate = $qbDate
            ->innerJoin('h2.bookings', 'b')
            ->where(':entryDate between b.entryDate and b.exitDate')
            ->orWhere(':exitDate between b.entryDate and b.exitDate')
            ->orWhere('b.entryDate between :entryDate and :exitDate');
        if ($entryDate && $exitDate) {
            $qb->where($qb->expr()->notIn('h.id',$qbDate->getDQL()))
                ->setParameter('entryDate', $entryDate)
                ->setParameter('exitDate', $exitDate);
        }
        if ($nbGuest) {
            //$qb->andWhere('h.availablePlaces >= :nbGuest')
                $qb->join('h.rooms', 'r')
                    ->join('r.bedRooms', 'br')
                    ->join('br.bed','bb')
                    ->groupBy('h')
                    ->having('SUM(br.quantity * bb.nbPlace) >= :nbGuest')
                   ->setParameter('nbGuest', $nbGuest);
        }
        if ($category) {
            $qb->andWhere('h.category = :category')
                ->setParameter('category', $category);
        }
        if ($city){
            $qb->addSelect("ACOS(SIN(PI()*h.latitude/180.0)*SIN(PI()*:lat2/180.0)+COS(PI()*h.latitude/180.0)*COS(PI()*:lat2/180.0)*COS(PI()*:lon2/180.0-PI()*h.longitude/180.0))*6371 AS dist")
                ->setParameter(":lat2", $city["ville_latitude_deg"])
                ->setParameter(":lon2", $city["ville_longitude_deg"])
                ->orderBy("dist");
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
