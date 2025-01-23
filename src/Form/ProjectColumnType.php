<?php
namespace App\Form;

use App\Entity\Project;
use App\Entity\ProjectColumn;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectColumnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Column Name',
                'attr' => [
                    'placeholder' => 'Enter column name'
                ]
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class, 
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
            'data_class' => ProjectColumn::class,
        ]);
    }
}