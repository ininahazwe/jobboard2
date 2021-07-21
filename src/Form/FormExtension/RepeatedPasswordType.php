<?php

namespace App\Form\FormExtension;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'label'             => "Password",
                'label_attr'        => [
                    'title'         => 'For security reasons, your password must contains...',
                ],
                'attr'              => [
                    'title'         => "For security reasons, your password must contains",
                    'maxlength'     => 255
                ]
            ],
            'second_options'        => [
                'label'             => "Confirmer le mot de passe",
                'label_attr'        => [
                    'title'         => "Confirmer le mot de passe"
                ],
                'attr'              => [
                    'title'         => "Confirm  password",
                    'maxlength'     => 255
                ]
            ],
        ]);
    }
}