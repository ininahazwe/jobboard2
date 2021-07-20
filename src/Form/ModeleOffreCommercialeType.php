<?php

namespace App\Form;

use App\Entity\ModeleOffreCommerciale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class ModeleOffreCommercialeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir un nom',
                    ]),
                ],
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir un slug',
                    ]),
                ],
            ])
            ->add('nombre_offres', IntegerType::class,[
                'required' => false
            ])
            ->add('isCvTheque')
            ->add('nombreRecruteurs', IntegerType::class, [
                'required' => false
            ])
            ->add('dureeContrat', TextType::class, [
                'required' => true,
                'label' => 'Duréee de contrat',
                'attr' => [
                    'placeholder' => 'En mois'
                ]
            ])
            ->add('prix', TextType::class, [
                'label' => 'Prix',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir un prix',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModeleOffreCommerciale::class,
        ]);
    }
}
