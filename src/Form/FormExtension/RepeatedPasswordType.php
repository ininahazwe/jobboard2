<?php

namespace App\Form\FormExtension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeatedPasswordType extends AbstractType
{
    public function getParent(): string
    {
        return RepeatedType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type'                  => PasswordType::class,
            'invalid_message'       => "Veuillez saisir le même mot de passe",
            'required'              => true,
            'first_options'         => [
/*                'label_attr'        => [
                    'title'         => 'Pour des raisons de sécurité, votre mot de passe doit contenir...',
                ],*/
                'attr'              => [
    /*                'title'         => "Pour des raisons de sécurité, votre mot de passe doit contenir...",*/
                    'maxlength'     => 255,
                    'placeholder' => 'Mot de passe'
                ]
            ],
            'second_options'        => [
                /*'label_attr'        => [
                    'title'         => "Confirmer le mot de passe"
                ],*/
                'attr'              => [
                   /* 'title'         => "Confirmer le mot de passe",*/
                    'maxlength'     => 255,
                    'placeholder'   => "Confirmer le mot de passe",
                ]
            ],
        ]);
    }
}