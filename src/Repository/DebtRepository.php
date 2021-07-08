<?php
declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Debt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Debt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Debt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Debt[]    findAll()
 * @method Debt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Debt::class);
    }

     /**
      * @return Debt[] Returns an array of Debt objects
      */
    public function findByDate(\DateTime $dateFrom, \DateTime $dateTo, ?string $symbol) : array
    {
        $query = $this->createQueryBuilder('d')
            ->andWhere('d.date >= :from')
            ->andWhere('d.date <= :to')
            ->setParameter('from', $dateFrom->format('Y-m-d'))
            ->setParameter('to', $dateTo->format('Y-m-d'));
        if ( ! is_null($symbol) ) {
            $query = $query
                ->andWhere('d.symbol LIKE :symbol')
                ->setParameter('symbol', '%' . addcslashes($symbol, "%_") . '%');
        }
        $query = $query
            ->orderBy('d.date', 'ASC')
            ->getQuery();
        return $query->getResult();
    }

    /**
     * @param array $ID list of Dept's IDs to delete from database
     */
    public function deletByIdArray(array $ID) : void
    {
        if ( is_array($ID) && count($ID) > 0 ) {
            $query = $this->createQueryBuilder('d')
                ->delete('App:Debt', 'd')
                ->andWhere('d.id IN (:ids)')
                ->setParameter('ids', $ID)
                ->getQuery();
            $query->execute();
        }
    }
}
