<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    /**
     * @param false $critique
     * @return mixed
     */
    public function getFacturesAPayer(bool $critique = false): mixed
    {
        $now = new \DateTime('now');
        $query = $this->createQueryBuilder('f')
            ->andWhere('f.isPaid = 0')
            ;
        if($critique){
            $query->andWhere('f.limiteDatePaid < :date')
                ->setParameter('date', $now);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function getFacturesPayees(): mixed
    {
        //$now = new DateTime('now');
        $query = $this->createQueryBuilder('f')
            ->andWhere('f.isPaid = 1')
        ;
        return $query->getQuery()->getResult();
    }
}
