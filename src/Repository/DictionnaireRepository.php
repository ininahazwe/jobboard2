<?php

namespace App\Repository;

use App\Entity\Dictionnaire;
use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dictionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dictionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dictionnaire[]    findAll()
 * @method Dictionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DictionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dictionnaire::class);
    }

    /**
     * @return Query
     */
    public function findAllActiveQuery(): Query
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = true')
            ->getQuery();
     }
    /**
     * @return array
     */
    public function findLatest(): array
    {
        return $this->findActiveQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }
    /**
     * @return QueryBuilder
     */
    public function findActiveQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.isActive = true');
    }

    public function getSecteursEntrepriseActifs(): array
    {
        $ids = array();
        $query = $this->getEntityManager()->getRepository(Entreprise::class)->createQueryBuilder('e')
            ->where('e.moderation = 1')
        ;

        $result = $query->getQuery()->getResult();
        foreach($result as $entreprise){
            if($entreprise->getSecteur()){
                $ids[] = $entreprise->getSecteur()->getId();
            }
        }
        return $ids;
    }

    // /**
    //  * @return Dictionnaire[] Returns an array of Dictionnaire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dictionnaire
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
