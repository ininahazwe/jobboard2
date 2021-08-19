<?php

namespace App\Form;

use App\Entity\Entreprise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAnnonceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mots', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrer un ou plusieurs mots-clés'
                ],
                'required' => false
            ])
            ->add('entreprise', EntityType::class, [
                'class' => Entreprise::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrer un ou plusieurs mots-clés'
                ],
                'required' => false
            ])
            ->add('Rechercher', SubmitType::class, [
                'attr' => [
                    'class' => 'btn primary'
                ]
            ])
       ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
    /*        'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false*/
        ]);
    }

    /*public function getBlockPrefix(): string
    {
        return '';
    }*/
}