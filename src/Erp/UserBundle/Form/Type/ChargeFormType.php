<?php

namespace Erp\UserBundle\Form\Type;

use Erp\UserBundle\Entity\Charge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChargeFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'text', ['label' => 'Amount', 'required' => true, 'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please enter amount to charge',
                            'groups' => ['LandlordCharge']
                        ]
                    )
                ]
            ])
            ->add('description', 'text', ['label' => 'Description', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('submit', 'submit', ['label' => 'Complete charge &amp; send URL by email', 'attr' => ['class' => 'btn edit-btn btn-space']]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Charge::class,
            'validation_groups' => ['LandlordCharge']
        ]);
    }

    public function getName()
    {
        return 'erp_user_landlords_charge';
    }
}
