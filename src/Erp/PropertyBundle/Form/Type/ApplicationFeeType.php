<?php

namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\PropertyBundle\Entity\ApplicationForm;

class ApplicationFeeType extends AbstractType
{
    const NAME = 'erp_property_application_fee';

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'noFee',
                'checkbox',
                [
                    'label' => 'No fee',
                    'required' => false,
                ]
            )
            ->add(
                'fee',
                'text',
                [
                    'label' => 'Fee',
                    'required' => false,
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
            'data_class' => ApplicationForm::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::NAME;
    }
}