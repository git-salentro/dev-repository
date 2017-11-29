<?php

namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\PropertyBundle\Entity\PropertySettings;

class PropertySettingsType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'paymentAcceptanceDateFrom',
                'sonata_type_date_picker',
                [
                    'label' => 'Payment Acceptance Date From'
                ]
            )
            ->add(
                'paymentAcceptanceDateTo',
                'sonata_type_date_picker',
                [
                    'label' => 'Payment Acceptance Date To'
                ]
            )
            ->add(
                'paymentAmount',
                'text',
                [
                    'label' => 'Payment Amount'
                ]
            )
            ->add(
                'allowPartialPayments',
                'checkbox',
                [
                    'label' => 'Allow Partial Payments',
                    'required' => false,
                ]
            )
            ->add(
                'allowCreditCardPayments',
                'checkbox',
                [
                    'label' => 'Allow Credit Card Payments',
                    'required' => false,
                ]
            )
            ->add(
                'allowAutoDraft',
                'checkbox',
                [
                    'label' => 'Set auto-draft from tenant account?',
                    'required' => false,
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertySettings::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_property_payment_settings';
    }
}