<?php

namespace Erp\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\PaymentBundle\Entity\StripeRecurringPayment;

class StripeRecurringPaymentType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'amount',
                'number',
                [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control subject',
                        'placeholder' => 'Amount $',
                    ],
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Amount should not be empty']),
                        new Assert\Length(
                            [
                                'min' => 1,
                                'max' => 4,
                                'minMessage' => 'Enter the amount in the range from $1 to $9999',
                                'maxMessage' => 'Enter the amount in the range from $1 to $9999',
                            ]
                        ),
                        new Assert\Range(
                            [
                                'min' => 0.01,
                                'max' => 1000000,
                                'minMessage' => 'Amount should have minimum 0.01$ and maximum $1,000,000',
                                'maxMessage' => 'Amount should have minimum 0.01$ and maximum $1,000,000',
                            ]
                        )
                    ],
                ]
            )
            ->add(
                'startPaymentAt',
                'date',
                [
                    'label' => false,
                    'attr' => [
                        'class' => 'form-control subject date',
                        'placeholder' => 'Payment Date',
                    ],
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Payment Date should not be empty']),
                        new Assert\Date(['message' => 'Is not a valid date format']),
                        new Assert\GreaterThanOrEqual(['value' => 'today', 'message' => 'Please select today\'s or future date.']),
                    ],
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                ]
            )
            ->add(
                'type',
                'checkbox',
                [
                    'required' => false,
                    'label' => 'Make this a recurring payment',
                    'label_attr' => ['class' => 'control-label'],
                ]
            )
            ->add(
                'submit',
                'submit',
                [
                    'label' => 'Make Payment',
                    'attr' => ['class' => 'btn red-btn'],
                ]
            );

        $builder->get('type')
            ->addViewTransformer(new CallbackTransformer(
                function ($type) {
                    return $type === StripeRecurringPayment::TYPE_RECURRING;
                },
                function ($type) {
                    return $type ? StripeRecurringPayment::TYPE_RECURRING : StripeRecurringPayment::TYPE_ONE;
                }
            ), true);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StripeRecurringPayment::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_payment_stripe_payyment_recurring';
    }
}
