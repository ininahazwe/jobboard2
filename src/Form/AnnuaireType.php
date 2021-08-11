<?php

namespace App\Form;

use App\Entity\Annuaire;
use App\Entity\Dictionnaire;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnuaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Description'
            ])
            ->add('web_link', UrlType::class, [
                'label' => 'Url',
                'required' => false
            ])
            ->add('addresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false
            ])
            ->add('code_postal', TextType::class, [
                'label' => 'Code postal',
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => false
            ])
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Dictionnaire::class,
                'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('d')
                        ->select('d')
                        ->where('d.type = :type')
                        ->setParameter('type', Dictionnaire::TYPE_CATEGORIE_ANNUAIRE);

                    return $query;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annuaire::class,
        ]);
    }
}
