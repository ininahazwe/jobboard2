<?php

namespace App\Form;

use App\Entity\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('createdAt')
            ->add('prix')
            ->add('TVA')
            ->add('prixTTC')
            ->add('isPaid')
            ->add('limiteDatePaid')
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
