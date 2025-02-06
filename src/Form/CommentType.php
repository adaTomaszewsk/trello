<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\Comment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Write a comment...'
                ]
            ])
            ->add('card', EntityType::class, [
                'class' => Card::class, 
                'choice_label' => 'id',
                'attr' => [
                    'style' => 'display:none;',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Add comment',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}