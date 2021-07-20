<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('birthdate', DateTimeType::class, [
                'date_widget' => 'single_text',
                'with_seconds' => false,
                'with_minutes' => false
            ])
            ->add('isRqth', CheckboxType::class, [
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir un slug',
                    ]),
                ],
            ])
            ->add('zipcode', TextType::class)
            ->add('diplome', TextType::class)
            ->add('experiences', TextType::class)
            ->add('zoneDeRecherche', TextType::class)
            ->add('metiers', TextType::class)
            ->add('isVisible', CheckboxType::class, [
                'required' => false
            ])
            ->add('isAmenagement', CheckboxType::class, [
                'required' => false
            ])
            ->add('cv', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
