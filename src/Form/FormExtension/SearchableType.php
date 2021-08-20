<?php

namespace App\Form\FormExtension;

use App\Entity\Entreprise;
use App\Entity\User;
use App\Entity\Dictionnaire;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchableType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function _construct(EntityManagerInterface $entityManager)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('class');
        $resolver->setDefaults([
            'compound' => false,
            'multiple' => true,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                function ($value): array {
                    return $value->map(fn($d) => (string)$d->getId())->toArray();
                },
                function (array $ids) use ($options) : Collection {
                    if(empty($ids)){
                        return new ArrayCollection([]);
                    }
                    return new ArrayCollection(
                        $this->entityManager->getRepository($options['class'])->findBy(['id' => $ids])
                    );
                }
            ))
            ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options,)
    {
        $view->vars['expanded'] = false;
        $view->vars['placeholder'] = null;
        $view->vars['placeholder_in_choices'] = false;
        $view->vars['multiple'] = true;
        $view->vars['preferred_choices'] = [];
        $view->vars['choices'] = $this->choices($form->getData());
        $view->vars['choice_translation_domain'] = false;
        $view->vars['full_name'] = '[]';
    }

    public function getBlockPrefix()
    {
        return 'choice';
    }

    private function choices(Collection $value)
    {
        return $value
            ->map(fn ($d) => new ChoiceView($d, (string)$d->getId(),(string)$d))
            ->toArray();
    }
}
