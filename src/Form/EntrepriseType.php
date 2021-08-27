<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Entity\Dictionnaire;
use App\Entity\Entreprise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('logo', FileType::class, [
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('regroupementCandidatures', ChoiceType::class, [
                'choices' => [
                    'Non' => '0',
                    'Oui' => '1'
                ],
                'label' => 'Regrouper les candidatures / candidat',
                'required' => true
            ])
            ->add('secteur', EntityType::class, [
                'required'  => false,
                'label' => 'Secteur',
                'expanded' => false,
                'class' => 'App\Entity\Dictionnaire',
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_SECTEUR);

                    return $query;
                }
            ])
            ->add('numeroSiren', IntegerType::class, [
                'required' => false
            ])
            ->add('numeroSiret', IntegerType::class, [
                'required' => false
            ])
            ->add('taille', IntegerType::class, [
                'required' => false
            ])
            ->add('adresse', CollectionType::class, [
                'entry_type' => AdresseType::class,
                'label' => 'Adresse',
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}
