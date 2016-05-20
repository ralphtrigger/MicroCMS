<?php

namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of UserType
 *
 * @author trigger
 */
class UserType extends AbstractType
{
    public function getName()
    {
        return 'user';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', 'text')
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('required' => true),
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat password'),
                ))
                ->add('role', 'choice', array(
                    'choices' => array('ROLE_ADMIN' => 'Admin', 'ROLE_USER' => 'User'),
        ));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MicroCMS\Domain\User',
        ));
    }


}
