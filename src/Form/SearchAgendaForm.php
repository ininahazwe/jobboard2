<?php

namespace App\Form;

use App\Data\SearchDataAgenda;
use App\Entity\Dictionnaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAgendaForm extends AbstractType
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
            ->add('category', EntityType::class, [
                'required'  => false,
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_CATEGORIE_AGENDA);

                    return $query;
                }
            ])
            ->add('virtuel', CheckboxType::class, [
                'label' => 'Virtuel',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchDataAgenda::class,
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
        return 'search_agenda';
    }
}