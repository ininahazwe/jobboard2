<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formule')
            ->add('nombre_offres')
            ->add('debutContratAt', DateTimeType::class, [
                'date_widget' => 'single_text',
                'with_minutes' => false,
                'with_seconds' => false
            ])
            ->add('finContratAt', DateTimeType::class, [
                'date_widget' => 'single_text',
                'with_minutes' => false,
                'with_seconds' => false
            ])
            ->add('isCvTheque')
            ->add('prix', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir un prix',
                    ]),
                ],
            ])
            ->add('facture')
            ->add('entreprise')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}
