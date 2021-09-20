<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Adresse;
use App\Entity\Dictionnaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchEntrepriseForm extends AbstractType
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
            ->add('secteur', EntityType::class, [
                'required'  => false,
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $ids = $repository->getSecteursEntrepriseActifs();
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->andWhere('d.id IN (:ids)')
                        ->setParameter('type', Dictionnaire::TYPE_SECTEUR)
                        ->setParameter('ids', $ids)
                    ;

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
                    $ids = $repository->getAdressesEntrepriseActifs();
                    $query = $repository->createQueryBuilder('a')
                        ->select('a')
                        ->where('a.entreprise IN (:ids)')
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
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'search_entreprise';
    }
}