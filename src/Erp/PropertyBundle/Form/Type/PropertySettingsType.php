<?php

namespace Erp\PropertyBundle\Form\Type;

use Erp\PropertyBundle\Entity\PropertySettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertySettingsType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $months = array_combine(range(1,31),range(1,31));
        $builder
            ->add(
                'paymentAcceptanceDateFrom',
                'choice',
                [
                    'label' => 'Payment Acceptance Date From',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control col-xs-4'],
                    'choices' => $months,
                    'choices_as_values' => true,
                ]
            )
            ->add(
                'paymentAcceptanceDateTo',
                'choice',
                [
                    'label' => 'Payment Acceptance Date To',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control col-xs-4'],
                    'choices' => $months,
                ]
            )
            ->add(
                'paymentAmount',
                'text',
                [
                    'label' => 'Payment Amount',
                    'label_attr'  => ['class' => 'control-label'],
                    'attr'        => ['class' => 'form-control col-xs-4'],
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

        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'submit']);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertySettings::class,
            'csrf_protection'   => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_property_payment_settings';
    }

    /**r
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        /** @var PropertySettings $data */
        $data = $event->getData();

        $paymentAcceptanceDateFrom = $data->getPaymentAcceptanceDateFrom();
        $paymentAcceptanceDateTo = $data->getPaymentAcceptanceDateTo();

        if ($paymentAcceptanceDateFrom > $paymentAcceptanceDateTo) {
            $form = $event->getForm();
            $form->get('paymentAcceptanceDateFrom')->addError(new FormError('Payment Acceptance Date must be greater then Payment Acceptance Date'));
        }
    }
}