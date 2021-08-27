<?php

namespace App\Form;

use App\Entity\Adresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{
    public function getParent(): string
    {
        return AdresseType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city' , TextType::class, [
                'empty_data' => "",
                'required' => false,
                'label' => 'Ville',
            ])
            ->add('zipcode' , TextType::class, [
                'empty_data' => "",
                'required' => false,
                'label' => 'Code postal',
            ])
            ->add('departement' , TextType::class, [
                'empty_data' => "",
                'required' => false,
                'label' => 'Ville',
            ])
            ->add('complement' , TextType::class, [
                'empty_data' => "",
                'required' => false,
                'label' => 'Ville',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
        ]);
    }

    public function getName(): string
    {
        return 'adresse';
    }
}
