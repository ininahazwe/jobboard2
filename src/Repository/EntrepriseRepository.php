<?php

namespace App\Repository;

use App\Data\SearchDataEntreprise;
use App\Entity\Annonce;
use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Entreprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entreprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entreprise[]    findAll()
 * @method Entreprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Entreprise::class);
        $this->paginator = $paginator;
    }

    public function findAllEntreprise($user)
    {
        $query = $this->createQueryBuilder('e');
        if($user->isSuperAdmin()){
            $query//->andWhere('a.isActive = true')
            ->addOrderBy('e.createdAt', 'DESC')
                ->getQuery();

            return $query;
        } elseif ($user->isSuperRecruteur() || $user->isRecruteur()){
            $ids = array();
            foreach($user->getRecruteursEntreprise() as $entreprise){
                if (!in_array($entreprise->getId(), $ids)){
                    $ids[$entreprise->getId()] = $entreprise->getId();
                }
            }
            foreach($user->getEntreprises() as $entreprise){
                if (!in_array($entreprise->getId(), $ids)){
                    $ids[$entreprise->getId()] = $entreprise->getId();
                }
            }

            $query->andWhere('e.id IN (:entreprises)')
                ->addOrderBy('e.createdAt', 'DESC')
                ->setParameter('entreprises', $ids)
                ->getQuery()
            ;

            return $query;
        }

    }

    /**
     * @param null $entreprise
     * @return mixed
     */
    public function getNbMaxRecruteurs($entreprise = null): mixed
    {
        $now = new \DateTime('now');
        $nombre = 0;
        $queryOffres = $this->getEntityManager()->getRepository('App\Entity\Offre')->createQueryBuilder('o');
        $queryOffres ->andWhere('o.entreprise = :entreprise')
            ->andWhere('o.debutContratAt <= :date')
            ->andWhere('o.finContratAt >= :date')
            ->setParameter('entreprise' , $entreprise)
            ->setParameter('date' , $now);
        $offres = $queryOffres->getQuery()->getResult();

        foreach ($offres as $offre)
        {
            if ($offre->getNombreRecruteurs() != null){
                $nombre = $nombre + $offre->getNombreRecruteurs();
                if ($offre->getNombreRecruteurs() == 0){
                    return $nombre = 0;
                }
            }else{
                $nombre = 0;
            }
        }
        return $nombre;
    }

    /**
     * @return int|string
     */
    public function genererRef(): int|string
    {
        $query = $this->getEntityManager()->getRepository('App\Entity\Entreprise')->createQueryBuilder('e');
        $query->addOrderBy('e.id' ,'DESC')
            ->setMaxResults(1);

        $entreprise = current($query->getQuery()->getResult());

        $referance = "10000001";
        if ($entreprise){
            $int_value = (int) $entreprise->getRefClient();
            $referance = $int_value + 1;
        }

        return $referance ;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getEntreprisesUser($user): mixed
    {
        $ids = array();

        if ($user->isSuperAdmin() ){
            $entreprises = $this->findAll();
            $ids = array();
            foreach($entreprises as $entreprise) {
                if ($entreprise->canCreateAnnonce()) {
                    $ids[$entreprise->getId()] = $entreprise->getId();
                }
            }
            $query = $this->createQueryBuilder('d')
                ->select('d')
                ->andWhere('d.id IN (:ids)')
                ->andWhere('d.moderation = 1')
                ->orderBy('d.name' ,  'ASC')
                ->setParameter('ids',$ids);
            return $query;

        }elseif($user->isSuperRecruteur()){
            $ids = array();
            foreach($user->getRecruteursEntreprise() as $entreprise) {
                if ($entreprise->canCreateAnnonce()){
                    if (!in_array($entreprise->getId(), $ids)) {
                        $ids[$entreprise->getId()] = $entreprise->getId();
                    }
                }
            }
            foreach($user->getEntreprises() as $entreprise) {
                if ($entreprise->canCreateAnnonce()){
                    if (!in_array($entreprise->getId(), $ids)) {
                        $ids[$entreprise->getId()] = $entreprise->getId();
                    }
                 }
            }
        }elseif($user->isRecruteur())
        {
            foreach($user->getEntreprises() as $entreprise){
                if ($entreprise->canCreateAnnonce()){
                    if (!in_array($entreprise->getId(), $ids)){
                        $ids[$entreprise->getId()] = $entreprise->getId();
                    }
                }
            }

        }
        $query = $this->createQueryBuilder('d')
            ->andWhere('d.id IN (:ids)')
            ->orderBy('d.name' ,  'ASC')
            ->setParameter('ids',$ids)
            ;
         return $query;
    }

    public function getEntrepriseHome(int $int)
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.logo', '_logo')
            ->having("COUNT(DISTINCT _logo.id) > 0")
            ->groupBy('e.id')
            ->orderBy('e.createdAt', 'DESC')
            ->orderBy('RAND()')
            ->setMaxResults($int)
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function getEntreprisesEnAttente(): mixed
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.moderation = 0')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function getEntreprisesAcceptees(): mixed
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.moderation = 1')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function getEntreprisesRefusees(): mixed
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.moderation = 2')
        ;
        return $query->getQuery()->getResult();
    }

    public function getEntreprisesAccepteesAvecAnnonces(): ArrayCollection
    {
        $query = $this->createQueryBuilder('e')
            ->select('e')
            ->where('e.moderation = 1')
        ;
        $result = $query->getQuery()->getResult();
        $entreprises = new ArrayCollection();
        foreach($result as $entreprise){
            if($entreprise->getNumberAnnoncesActive() > 0){
                $entreprises->add($entreprise);
            }
        }

        return $entreprises;
    }

    public function getEntreprisesAnnoncesPubliees(): array
    {
        $ids = array();
        $now = new \DateTime('now');
        $query = $this->getEntityManager()->getRepository(Annonce::class)->createQueryBuilder('a')
            ->andWhere('a.isActive = 1')
            ->andWhere('a.dateLimiteCandidature > :date')
            ->setParameter('date', $now)

        ;
        $result = $query->getQuery()->getResult();
        foreach($result as $annonce){
            $ids[] = $annonce->getEntreprise()->getId();
        }
        return $ids;
    }

    public function getEntrepriseActive()
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.moderation = 1')
        ;
        return $query->getQuery()->getResult();
    }

    //Recherche m??thode GrafikArt

    /**
     * @param SearchDataEntreprise $search
     * @return PaginationInterface
     */
    public function findSearch(SearchDataEntreprise $search): PaginationInterface
    {
        $query = $this->getSearchQuery($search)->getQuery();

        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }

    public function getSearchQuery (SearchDataEntreprise $search): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('e')
            ->where('e.moderation = 1')
            //->select('e', 's')
            ->join('e.secteur', 's');


        if(!empty($search->q)){
            $query = $query
                ->andWhere('e.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if(!empty($search->secteur)){
            $query = $query
                ->andWhere('s.id IN (:secteur)')
                ->setParameter('secteur', $search->secteur);
        }

        if(!empty($search->adresse)){
            $query
                ->innerJoin('e.adresse', 'c')
                ->andWhere('c.id IN (:adresse)')
                ->setParameter('adresse', $search->adresse);
        }

        return $query;
    }
}
