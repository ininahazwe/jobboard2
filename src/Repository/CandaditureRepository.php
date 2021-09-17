<?php

namespace App\Repository;

use App\Entity\Candidature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Candidature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Candidature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Candidature[]    findAll()
 * @method Candidature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandaditureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidature::class);
    }

    public function hasCandidature($user, $annonce): ?Candidature
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.candidat = :candidat')
            ->innerJoin('c.annonces', 'annonces');

        $query->andWhere($query->expr()->in('annonces.id', array($annonce->getId())))
            ->setParameter('candidat', $user);

        return $query->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getAllCandidatures($user)
    {
        $query = $this->createQueryBuilder('c');
        if($user->isSuperAdmin()){
            $query
                ->orderBy('c.createdAt', 'DESC');
        } elseif ($user->isSuperRecruteur() || $user->isRecruteur()){
            $entreprises = $user->getEntrepriseAll();
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.entreprise IN(:entreprises)')
                ->setParameter('entreprises', $entreprises)
                ;
        }elseif($user->isCandidat()){
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.candidat = :user')
                ->setParameter('user', $user)
                ;
        }else{
            return null;
        }
        return $query->getQuery()->getResult();
    }


    public function hasCandidatureEntreprise($user, $entreprise): ?Candidature
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->andWhere('c.candidat = :candidat')
            ->andWhere('c.entreprise = :entreprise');

        $query
            ->setParameter('entreprise', $entreprise)
            ->setParameter('candidat', $user)
            ->setMaxResults(1)
        ;

        return $query->getQuery()
            ->getOneOrNullResult()
            ;
    }


    public function getCountOnDashboard(): array|int|string
    {
        $query = $this->createQueryBuilder('c')
            ->select('c');
        return $query
            ->getQuery()->getScalarResult();
    }

    public function getCandidaturesAcceptees($user)
    {
        $query = $this->createQueryBuilder('c');
        if($user->isSuperAdmin()){
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.statut = 1');
        } elseif ($user->isSuperRecruteur() || $user->isRecruteur()){
            $entreprises = $user->getEntrepriseAll();
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.entreprise IN(:entreprises)')
                ->andWhere('c.statut = 1')
                ->setParameter('entreprises', $entreprises)
            ;
        }elseif($user->isCandidat()){
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.candidat = :user')
                ->setParameter('user', $user)
            ;
        }else{
            return null;
        }
        return $query->getQuery()->getResult();
    }

    public function getCandidaturesRefusees($user)
    {
        $query = $this->createQueryBuilder('c');
        if($user->isSuperAdmin()){
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.statut = 2');
        } elseif ($user->isSuperRecruteur() || $user->isRecruteur()){
            $entreprises = $user->getEntrepriseAll();
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.entreprise IN(:entreprises)')
                ->andWhere('c.statut = 2')
                ->setParameter('entreprises', $entreprises)
            ;
        }elseif($user->isCandidat()){
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.candidat = :user')
                ->setParameter('user', $user)
            ;
        }else{
            return null;
        }
        return $query->getQuery()->getResult();
    }

    public function getCandidaturesAttente($user)
    {
        $query = $this->createQueryBuilder('c');
        if($user->isSuperAdmin()){
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.statut = 0');
        } elseif ($user->isSuperRecruteur() || $user->isRecruteur()){
            $entreprises = $user->getEntrepriseAll();
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.entreprise IN(:entreprises)')
                ->andWhere('c.statut = 0')
                ->setParameter('entreprises', $entreprises)
            ;
        }elseif($user->isCandidat()){
            $query
                ->orderBy('c.createdAt', 'DESC')
                ->andWhere('c.candidat = :user')
                ->setParameter('user', $user)
            ;
        }else{
            return null;
        }
        return $query->getQuery()->getResult();
    }

}
