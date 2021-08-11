<?php

namespace App\Form;

use App\Entity\Dictionnaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreationEntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('secteur', EntityType::class, [
                'label' => 'Secteur',
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_SECTEUR);

                    return $query;
                }
            ])
            ->add('siret', NumberType::class, [
                'label' => 'Numéro de siret',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('siren', NumberType::class, [
                'label' => 'Numéro de siren',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('taille', NumberType::class, [
                'label' => 'Taille de l\'entreprise',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}