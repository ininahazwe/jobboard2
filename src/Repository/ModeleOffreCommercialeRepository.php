<?php

namespace App\Repository;

use App\Entity\ModeleOffreCommerciale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModeleOffreCommerciale|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModeleOffreCommerciale|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModeleOffreCommerciale[]    findAll()
 * @method ModeleOffreCommerciale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeleOffreCommercialeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModeleOffreCommerciale::class);
    }
}
