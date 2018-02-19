<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LandlordFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', ['required' => true, 'attr' => ['placeholder' => '', 'class' => 'form-control']])
            ->add('phone', 'text', ['required' => true, 'attr' => ['placeholder' => '', 'class' => 'form-control']])
            ->add('email', 'text', ['required' => true, 'attr' => ['placeholder' => '', 'class' => 'form-control']])
            ->add('addressOne', 'text', ['required' => false, 'attr' => ['placeholder' => '', 'class' => 'form-control']])
            ->add('save','submit', ['label' => 'Save', 'attr' => ['class' => 'btn edit-btn']]
            );
    }

    public function getName()
    {
        return 'erp_user_landlords_create';
    }
}
