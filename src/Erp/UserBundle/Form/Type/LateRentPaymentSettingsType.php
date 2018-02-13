<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\UserBundle\Entity\LateRentPaymentSettings;

class LateRentPaymentSettingsType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'fee',
                'money',
                [
                    'label' => 'Fee',
                    'currency' => false,
                ]
            )
            ->add(
                'allowRentPayment',
                'checkbox',
                [
                    'label' => 'Allow Rent Payment',
                ]
            )
            ->add(
                'category',
                'choice',
                [
                    'label' => 'Category',
                    'choices' => [
                        //TODO Refactoring fee tenant payment
                        'fee' => 'Late Fees',
                    ]
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LateRentPaymentSettings::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_user_late_rent_payment';
    }
}