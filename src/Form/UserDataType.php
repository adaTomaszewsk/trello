<?php 

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('firstName', TextType::class, [
            'label' => 'First Name',
            'required' => true
        ])
        ->add('lastName', TextType::class, [
            'label' => 'Last Name',
            'required' => true
        ])
        ->add('phoneNumber', TextType::class, [
            'label' => 'Phone Number',
            'required' => false
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Add list',
            'attr' => [
                'class' => 'btn btn-primary',
            ]
        ])
        ->getForm();
    }
}