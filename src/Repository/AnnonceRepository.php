<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\User;
use App\Form\AnnonceType;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function findAllActiveQuery($user): QueryBuilder
    {
        $query = $this->createQueryBuilder('a');
        if ($user->isSuperAdmin()) {
            $query//->andWhere('a.isActive = true')
            ->addOrderBy('a.createdAt', 'DESC')
                ->getQuery();

            return $query;
        } elseif ($user->isSuperRecruteur() || $user->isRecruteur()) {
            $ids = $user->getEntrepriseIds();
            $query->andWhere('a.entreprise IN (:entreprises)')
                ->addOrderBy('a.createdAt', 'DESC')
                ->setParameter('entreprises', $ids)
                ->getQuery();

            return $query;
        }
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
     * @param null $limite
     * @return array
     */
    public function findActiveAndLive($limite=null): array
    {
        $now = new DateTime('now');
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.isActive = 1')
            ->andWhere('a.dateLimiteCandidature > :date')
            ->setParameter('date', $now)
        ;
        if($limite){
            $query->setMaxResults($limite);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findActiveQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.isActive = 1');
    }

    /**
     * @param null $mots
     * @return mixed
     */
    public function search($mots = null): mixed
    {
        $query = $this->createQueryBuilder('a');
        $query->where('a.isActive = 1');
        if($mots != null){
            $query->andWhere('MATCH_AGAINST(a.name, a.description) AGAINST (:mots boolean)>0')
                ->setParameter('mots', $mots);
        }
        return $query->getQuery()->getResult();
    }

    public function searchAnnoncesAdvanced(mixed $criteria)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.entreprise', 'entreprise')
            ->where('entreprise.name = :entreprise')
            ->setParameter("entreprise", $criteria['entreprise']->getName())
            /*->andWhere('c.type_contrat = :type_contrat')
            ->setParameter('type_contrat', $criteria['type_contrat'])
            ->andWhere('c.diplome = :diplome')
            ->setParameter('diplome', $criteria['diplome'])*/
            ->getQuery()->getResult();
    }
}
