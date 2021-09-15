<?php

namespace App\Form;

use App\Entity\Entreprise;
use App\Entity\User;
use App\Entity\Dictionnaire;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description'
            ])
            ->add('isActive', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'required' => false
            ])
            ->add('diplome', EntityType::class, [
                'required'  => true,
                'label' => 'Diplôme requis',
                'expanded' => false,
                'class' => 'App\Entity\Dictionnaire',
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_DIPLOMA);

                    return $query;
                }
            ])
            ->add('experience', EntityType::class, [
                'required'  => false,
                'label' => 'Experience',
                'expanded' => false,
                'class' => 'App\Entity\Dictionnaire',
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_EXPERIENCE);

                    return $query;
                }
            ])
            ->add('adresse', CollectionType::class, [
                'entry_type' => AdresseType::class,
                'label' => 'Adresse',
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('entreprise', EntityType::class ,[
                'class' => Entreprise::class,
                'query_builder' => function($repository) use($user) {
                    return $repository->getEntreprisesUser($user);
                }
            ])
            ->add('auteur', EntityType::class,[
                'required' => false,
                'label'	=> "Gestionnaire(s) de l'offre d'emploi",
                'multiple' => true,
                'class' => User::class,
                'query_builder' => function($repository) use($user) {
                    return $repository->getEntreprisesRecruteur($user);
                },
                'by_reference' => false,
                'attr' => [
                    'class' => 'select-authors'
                ]
            ])
            ->add('reference', TextType::class)
            ->add('dateLimiteCandidature', DateTimeType::class, [
                'date_widget' => 'single_text',
                'with_minutes' => false,
                'with_seconds' => false
            ])
            ->add('type_contrat', EntityType::class, [
                'required'  => false,
                'label' => 'Type de contrat',
                'multiple' => true,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_CONTRACT);

                    return $query;
                }
            ])
            ->add('lien', UrlType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
        ]);
        $resolver->setRequired([
            'user',
        ]);
    }
}
