<?php

namespace App\Form;

use App\Entity\Candidature;
use App\Entity\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatureType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('lettre_motivation', EntityType::class, [
        'class' => File::class,
        'mapped' => false,
        'required' => false
        /*'query_builder' => function($repository) use($user) {
            return $repository->findBy(['user' => $user]);
        }*/
      ])
      ->add('cv', EntityType::class, [
        'class' => File::class,
        'mapped' => false,
        'required' => false
        /*'query_builder' => function($repository) use($user) {
          return $repository->findBy(['user' => $user]);
        }*/
      ])
      ->add('Valider', SubmitType::class);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => Candidature::class,
    ]);

  }
}
