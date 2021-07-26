<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Dictionnaire;
use App\Entity\Entreprise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchAnnonceAdvancedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('diplome', EntityType::class, [
                'required'  => false,
                'label' => 'Type de contrat',
                'expanded' => false,
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
            ->add('type_contrat', EntityType::class, [
                'required'  => false,
                'label' => 'Type de contrat',
                'expanded' => false,
                'multiple' => true,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_CONTRACT);

                    return $query;
                }
            ])*/
            ->add('entreprise', EntityType::class, [
                'class' => Entreprise::class
            ])
            ->add('Rechercher', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-sm btn-primary mt-2'
                ]
            ])
            ;
    }
}