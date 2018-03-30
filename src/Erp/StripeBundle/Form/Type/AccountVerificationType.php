<?php

namespace Erp\StripeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Erp\PaymentBundle\Entity\StripeAccount;
use Symfony\Component\Validator\Constraints as Assert;

class AccountVerificationType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'city',
                'text',
                [
                    'label' => 'City',
                ]
            )
            ->add(
                'line1',
                'text',
                [
                    'label' => 'Line 1',
                ]
            )
            ->add(
                'postalCode',
                'text',
                [
                    'label' => 'Postal Code',
                ]
            )
            ->add(
                'state',
                'text',
                [
                    'label' => 'State',
                ]
            )
            ->add(
                'businessName',
                'text',
                [
                    'label' => 'Business Name',
                ]
            )
            ->add(
                'businessTaxId',
                'text',
                [
                    'label' => 'Business Tax Id',
                ]
            )
            ->add(
                'dayOfBirth',
                'text',
                [
                    'label' => 'Day Of Birth',
                    'attr' => [
                        'placeholder' => '01',
                    ],
                    'constraints' => [
                        new Assert\Regex(
                            [
                                'message' => 'Invalid Format',
                                'pattern' => '(0[1-9]|[12][0-9]|3[01])',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'monthOfBirth',
                'text',
                [
                    'label' => 'Month Of Birth',
                    'attr' => [
                        'placeholder' => '01',
                    ],
                    'constraints' => [
                        new Assert\Regex(
                            [
                                'message' => 'Invalid Format',
                                'pattern' => '^(0?[1-9]|1[012])$'
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'yearOfBirth',
                'text',
                [
                    'label' => 'Year Of Birth',
                    'attr' => [
                        'placeholder' => '1999',
                    ],
                    'constraints' => [
                        new Assert\Regex(
                            [
                                'message' => 'Invalid Format',
                                'pattern' => '^19\d{2}$'
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'firstName',
                'text',
                [
                    'label' => 'First Name',
                ]
            )
            ->add(
                'lastName',
                'text',
                [
                    'label' => 'Last Name',
                ]
            )
            ->add(
                'ssnLast4',
                'text',
                [
                    'label' => 'SSN Last 4 digits',
                ]
            )
            ->add(
                'tosAcceptance',
                'checkbox',
                [
                    'label' => 'Term of use',
                    'mapped' => false,
                    'constraints' => new IsTrue([
                        'message' => 'Please indicate that you have read and agree to the Terms and Conditions and Privacy Policy'
                    ]),
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StripeAccount::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_stripe_bank_account_verification';
    }
}