<?php

namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;

class StopAutoWithdrawalFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'endAt',
                'text',
                [
                    'required' => false,
                    'label' => 'Date',
                ]
            )
            ->add(
                'submit',
                'submit',
                [
                    'label' => 'Submit',
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ScheduledRentPayment::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_property_stop_auto_withdrawal';
    }
}