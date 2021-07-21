<?php

namespace App\Form;

use App\Entity\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prix', TextType::class)
            ->add('TVA', TextType::class)
            ->add('prixTTC', TextType::class)
            ->add('isPaid')
            ->add('limiteDatePaid', DateTimeType::class, [
                'date_widget' => 'single_text',
                'with_minutes' => false,
                'with_seconds' => false
            ])
            ->add('paymentDate', DateType::class, [
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('paymentMethods')
            ->add('linkFacture')
            ->add('entreprise')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
