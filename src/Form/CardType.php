<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\ProjectColumn;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter a title or paste a link'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Enter a description'
                ]
                ])
            ->add('project_column', EntityType::class, [
                'class' => ProjectColumn::class, 
                'choice_label' => 'id',
                'attr' => [
                    'style' => 'display:none;',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Add list',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}