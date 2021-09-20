<?php

namespace App\Form;

use App\Data\SearchDataAnnonces;
use App\Entity\Adresse;
use App\Entity\Dictionnaire;
use App\Entity\Entreprise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAnnonceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('entreprises', EntityType::class, [
                'required' => false,
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'class' => Entreprise::class,
                'query_builder' => function($repository) {
                    $ids = $repository->getEntreprisesAnnoncesPubliees();
                    $query = $repository->createQueryBuilder('e')
                        ->select('e')
                        ->where('e.moderation = 1')
                        ->andWhere('e.id IN (:ids)')
                        ->setParameter('ids', $ids)
                    ;

                    return $query;
                }
            ])
            ->add('contrat', EntityType::class, [
                'required'  => false,
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_CONTRACT);

                    return $query;
                }
            ])
            ->add('diplome', EntityType::class, [
                'required'  => false,
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_DIPLOMA);

                    return $query;
                }
            ])
            ->add('experience', EntityType::class, [
                'required'  => false,
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_EXPERIENCE);

                    return $query;
                }
            ])
            ->add('adresse', EntityType::class, [
                'required' => false,
                'label' => false,
                'expanded' => true,
                'multiple'=> true,
                'class' => Adresse::class,
                'query_builder' => function($repository) {
                    $ids = $repository->getAdressesAnnoncesActives();
                    $query = $repository->createQueryBuilder('a')
                        ->select('a')
                        ->where('a.annonce IN (:ids)')
                        ->setParameter('ids', $ids)
                    ;
                    return $query;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchDataAnnonces::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function getName(): string
    {
        return 'search_annonces';
    }
}