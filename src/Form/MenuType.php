<?php

namespace App\Form;

use App\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez saisir un nom',
                    ]),
                ],
            ])
            ->add('link', TextType::class, [
                'required' => false
            ])
            ->add('display_order')
            ->add('isParent')
            ->add('niveau',  ChoiceType::class, [
                'choices' => Menu::getNiveauList()
            ])
            ->add('type',  ChoiceType::class, [
                'choices' => Menu::getTypeList()
            ])
            ->add('contenu',  TextareaType::class, [
                'required' => false
            ])
            ->add('childMenu', EntityType::class, [
                'required'  => false,
                'label' => 'Menu parent',
                'class' => Menu::class,
                'choice_label' => function ($menu){
                    return $menu->getNameNiveau();
                },
               'query_builder' => function($repository) {
                    $query = $repository->createQueryBuilder('m')
                        ->select('m')
                        ->orderBy('m.display_order', 'ASC');

                    return $query;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
