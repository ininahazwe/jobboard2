<?php

namespace App\Form;

use App\Data\SearchDataAnnuaire;
use App\Entity\Adresse;
use App\Entity\Dictionnaire;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAnnuaireForm extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('q', TextType::class, [
                        'label' => false,
                        'required' => false,
                        'attr' => [
                                'placeholder' => 'Rechercher'
                        ]
                ])
                ->add('category', EntityType::class, [
                        'required' => false,
                        'label' => false,
                        'expanded' => true,
                        'multiple' => true,
                        'class' => Dictionnaire::class,
                        'query_builder' => function ($repository) {
                            $query = $repository->createQueryBuilder('d')
                                    ->select('d')
                                    ->where('d.type = :type')
                                    ->setParameter('type', Dictionnaire::TYPE_CATEGORIE_ANNUAIRE);

                            return $query;
                        }
                ])
                ->add('adresse', EntityType::class, [
                        'required' => false,
                        'label' => false,
                        'expanded' => true,
                        'multiple' => true,
                        'class' => Adresse::class,
                        'query_builder' => function ($repository) {
                            $query = $repository->createQueryBuilder('a')
                                    ->select('a')
                                    ->orderBy('a.city', 'ASC')
                                    ->where('a.annuaire IS NOT NULL')
                                    ->andWhere('a.city is NOT NULL')
                                    ->distinct()
                            ;
                            return $query;
                        }
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
                'data_class' => SearchDataAnnuaire::class,
                'method' => 'GET',
                'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string {
        return '';
    }

    public function getName(): string {
        return 'search_annuaire';
    }
}