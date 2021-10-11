<?php

namespace App\Repository;

use App\Data\SearchDataAnnuaire;
use App\Entity\Annuaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Annuaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annuaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annuaire[]    findAll()
 * @method Annuaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnuaireRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Annuaire::class);
        $this->paginator = $paginator;
    }

    public function sortAlphabetically() {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC');
        return $qb->getQuery()->getResult();
    }


    /**
     * @param SearchDataAnnuaire $search
     * @return PaginationInterface
     */
    public function findSearch(SearchDataAnnuaire $search): PaginationInterface
    {
        $query = $this->getSearchQuery($search)->getQuery()->getResult();

        return $this->paginator->paginate(
                $query,
                $search->page,
                100
        );
    }

    public function getSearchQuery (SearchDataAnnuaire $search): QueryBuilder
    {
        $query = $this
                ->createQueryBuilder('a');

        //->join('a.contrat', 'c');

        if(!empty($search->q)){
            $query
                    ->andWhere('a.title LIKE :q')
                    ->setParameter('q', "%" . $search->q . "%");
        }
        if(!empty($search->category)){
            $query
                    ->innerJoin('a.categorie', 'c')
                    ->andWhere('c.id IN (:categorie_annuaire)')
                    ->setParameter('categorie_annuaire', $search->category);
        }
        if(!empty($search->adresse)){
            $query
                    ->innerJoin('a.adresse', 'd')
                    ->andWhere('d.id IN (:adresse)')
                    ->setParameter('adresse', $search->adresse);
        }

        return $query;
    }
}
