<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contacts',
                TextType::class,
                ['required'   => false, 'attr' => array('maxlength' => '12','pattern' => '\+79[0-9]{9}')]
            );
    }

    public function getParent()
    {
        return BaseProfileFormType::class;
    }
}
