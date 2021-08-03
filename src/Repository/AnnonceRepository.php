<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Annonce;
use DateTime;
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
     * @param SearchData $search
     * @return PaginationInterface
     */
    public function findSearch(SearchData $search): PaginationInterface
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
    }
}
