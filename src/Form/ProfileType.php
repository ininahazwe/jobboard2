<?php

namespace App\Form;

use App\Entity\Dictionnaire;
use App\Entity\Profile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civilite', EntityType::class, [
                'required'  => false,
                'label' => 'Civilité',
                'expanded' => true,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_CATEGORIE_CIVILITE);

                    return $query;
                }
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('birthdate', DateTimeType::class, [
                'date_widget' => 'single_text',
                'with_seconds' => false,
                'with_minutes' => false
            ])
            ->add('isRqth', CheckboxType::class, [
                'required' => false,
                'label' => 'J\'ai le statut RQTH'
            ])
            ->add('adresse', CollectionType::class, [
                'entry_type' => AdresseType::class,
                'label' => 'Adresse',
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('diplome', EntityType::class, [
                'required'  => false,
                'label' => 'Diplôme',
                'expanded' => false,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_DIPLOMA);

                    return $query;
                }
            ])
            ->add('experiences', EntityType::class, [
                'required'  => false,
                'label' => 'Expérience',
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
            ->add('zoneDeRecherche', TextType::class)
            ->add('metiers', EntityType::class, [
                'required'  => false,
                'label' => 'Métier',
                'expanded' => false,
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_METIER);

                    return $query;
                }
            ])
            ->add('isVisible', CheckboxType::class, [
                'required' => false,
                'label' => 'Rendre le profile visible aux recruteurs'
            ])
            ->add('isAmenagement', CheckboxType::class, [
                'required' => false,
                'label' => 'Besoin d\'aménagement?'
            ])
            ->add('cv',  DropzoneType::class, [
              'attr' => [
                'placeholder' => 'Choisir un fichier',
                'data-controller' => 'mydropzone'
              ],
              'label' => false,
              'multiple' => false,
              'mapped' => false,
              'required' => false,
            ])
            ->add('portfolio', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
