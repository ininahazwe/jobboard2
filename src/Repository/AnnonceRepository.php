<?php

namespace App\Repository;

use App\Data\SearchDataAnnonces;
use App\Entity\Annonce;
use App\Entity\Dictionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Annonce::class);
        $this->paginator = $paginator;
    }

    public function findAllActiveQuery($user)
    {
        $query = $this->createQueryBuilder('a');
        if ($user->isSuperAdmin()) {
            $query->addOrderBy('a.createdAt', 'DESC')
                ;
            return $query->getQuery()->getResult();
        }
        $ids = $user->getEntrepriseIds();
        $query->andWhere('a.entreprise IN (:entreprises)')
            ->addOrderBy('a.createdAt', 'DESC')
            ->setParameter('entreprises', $ids)
            ;

        return $query->getQuery()->getResult();

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

    public function getAnnoncesEntreprise()
    {
        return $this->createQueryBuilder('a')
            ->join('a.auteur', 'e', 'WITH', 'e = a.id')
            ->getQuery()->getResult();
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
     * @param SearchDataAnnonces $search
     * @return PaginationInterface
     */
    public function findSearch(SearchDataAnnonces $search): PaginationInterface
    {
        $query = $this->getSearchQuery($search)->getQuery()->getResult();

        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }

    public function getSearchQuery (SearchDataAnnonces $search): QueryBuilder
    {
        $now = new \DateTime('now');
        $query = $this
            ->createQueryBuilder('a')
            ->andWhere('a.isActive = 1')
            ->andWhere('a.dateLimiteCandidature > :date')
            ->setParameter('date', $now);

        //->join('a.contrat', 'c');

        if(!empty($search->q)){
            $query
                ->andWhere('a.name LIKE :q')
                ->setParameter('q', "%" . $search->q . "%");
        }

        if(!empty($search->entreprises)){
            $query
                ->innerJoin('a.entreprise', 'e')
                ->andWhere('e.id IN (:entreprises)')
                ->setParameter('entreprises', $search->entreprises);
        }
        if(!empty($search->contrat)){
            $query
                ->innerJoin('a.type_contrat', 'c')
                ->andWhere('c.id IN (:contrat)')
                ->setParameter('contrat', $search->contrat);
        }
        if(!empty($search->diplome)){
            $query
                ->innerJoin('a.diplome', 'c')
                ->andWhere('c.id IN (:diplome)')
                ->setParameter('diplome', $search->diplome);
        }
        if(!empty($search->experience)){
            $query
                ->innerJoin('a.experience', 'c')
                ->andWhere('c.id IN (:experience)')
                ->setParameter('experience', $search->experience);
        }

        return $query;
    }

    public function findAnnoncesEnFavori($user)
    {
        $now = new \DateTime('now');

        $query = $this->createQueryBuilder('a')
            ->andWhere('a.isActive = 1')
            ->andWhere('a.dateLimiteCandidature >= :date')
            ->innerJoin('a.favoris', 'u', 'WITH', 'u.id = :user')
            ->setParameter('user', $user)
            ->setParameter('date', $now);

        return $query
            ->getQuery()->getResult();
    }

    public function getAnnoncesArchivees(): mixed
    {
        $now = new \DateTime('now');
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.dateLimiteCandidature < :date')
            ->setParameter('date', $now)
        ;
        return $query->getQuery()->getResult();
    }

    public function getAnnoncesAttente(): mixed
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.isActive = 0')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * Returns number of "Annonces" per day
     * @return void
     */
    public function countByDate(){
        // $query = $this->createQueryBuilder('a')
        //     ->select('SUBSTRING(a.created_at, 1, 10) as dateAnnonces, COUNT(a) as count')
        //     ->groupBy('dateAnnonces')
        // ;
        // return $query->getQuery()->getResult();
        $query = $this->getEntityManager()->createQuery("
            SELECT SUBSTRING(a.created_at, 1, 10) as dateAnnonces, COUNT(a) as count FROM App\Entity\Annonces a GROUP BY dateAnnonces
        ");
        return $query->getResult();
    }
}
