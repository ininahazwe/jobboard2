<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
        $now = new \DateTime('now');
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
     * @param $entreprise
     * @return mixed
     */
    public function getAnnoncesEntreprise($entreprise): mixed
    {
        $now = new \DateTime('now');
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.isActive = 1')
            ->andWhere('a.entreprise = :entreprise')
            ->andWhere('a.dateLimiteCandidature > :date')
            ->setParameter('date', $now)
            ->setParameter('entreprise', $entreprise)
        ;

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

    /*public function findSearch(SearchData $search): PaginationInterface
    {
        $query = $this
            ->createQueryBuilder('a')
            ->select('e', 'a')
            ->join('a.entreprise', 'e')
        ;

        if(!empty($search->q)){
            $query = $query
                ->andWhere('MATCH_AGAINST(a.name, a.description) AGAINST (:q boolean)>0')
                ->setParameter('q', "%{$search->q}%");
        }
        if(!empty($search->entreprise)){
            $query = $query
                ->andWhere('e.id IN (:entreprise)')
                ->setParameter('entreprise', $search->entreprise);
        }

        $query = $query->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            6
        );
    }*/

    /*Search Engine*/

    public function search($mots = null, $entreprises = null)
    {
        $query = $this->createQueryBuilder('a');
        $query->where('a.isActive = 1');

        if($mots != null){
            $query->andWhere('MATCH_AGAINST(a.name, a.description) AGAINST (:mots boolean)>0')
                ->setParameter('mots', $mots);
        }

        if($entreprises != null){
            $query->leftJoin('a.entreprise', 'e');
            $query->andWhere('e.id = :id')
                ->setParameter('id', $entreprises);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param $page
     * @param $limit
     * @param null $filters
     * @return mixed
     */
    public function getPaginatedAnnonces($page, $limit, $filters = null): mixed
    {
        $now = new \DateTime('now');
        $query = $this->createQueryBuilder('a')
            ->where('a.isActive = 1')
            ->andWhere('a.dateLimiteCandidature > :date')
            ->setParameter('date', $now)
        ;

        // filtre des données
        if($filters != null){
            $query->andWhere('a.entreprise IN(:entreprise)')
                ->setParameter(':entreprise', array_values($filters));
        }

        $query->orderBy('a.createdAt')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
        ;
        return $query->getQuery()->getResult();
    }


    /**
     * @param null $filters
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalAnnonces($filters = null): mixed
    {
        $query = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.isActive = 1');
        // filtre des données
        if($filters != null){
            $query->andWhere('a.entreprise IN(:entreprise)')
                ->setParameter(':entreprise', array_values($filters));
        }

        return $query->getQuery()->getSingleScalarResult();
    }
}
