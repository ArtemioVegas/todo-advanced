<?php
namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username',null,['label' => 'Логин','required' => true])
            ->add('_password', PasswordType::class,['label' => 'Пароль','required' => true])
            ->add('captcha', CaptchaType::class)
        ;
    }

    public function getBlockPrefix() {
        return null;
    }
}