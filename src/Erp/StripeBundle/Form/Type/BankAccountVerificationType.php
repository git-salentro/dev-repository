<?php

namespace Erp\StripeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Erp\PaymentBundle\Entity\StripeAccount;

class BankAccountVerificationType extends AbstractType
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
                    'required' => false,
                    'label' => 'City',
                ]
            )
            ->add(
                'line1',
                'text',
                [
                    'required' => false,
                    'label' => 'Line 1',
                ]
            )
            ->add(
                'postalCode',
                'text',
                [
                    'required' => false,
                    'label' => 'Postal Code',
                ]
            )
            ->add(
                'state',
                'text',
                [
                    'required' => false,
                    'label' => 'State',
                ]
            )
            ->add(
                'businessName',
                'text',
                [
                    'required' => false,
                    'label' => 'Business Name',
                ]
            )
            ->add(
                'businessTaxId',
                'text',
                [
                    'required' => false,
                    'label' => 'Business Tax Id',
                ]
            )
            ->add(
                'dayOfBirth',
                'text',
                [
                    'required' => false,
                    'label' => 'Day Of Birth',
                ]
            )
            ->add(
                'monthOfBirth',
                'text',
                [
                    'required' => false,
                    'label' => 'Month Of Birth',
                ]
            )
            ->add(
                'yearOfBirth',
                'text',
                [
                    'required' => false,
                    'label' => 'Year Of Birth',
                ]
            )
            ->add(
                'firstName',
                'text',
                [
                    'required' => false,
                    'label' => 'First Name',
                ]
            )
            ->add(
                'lastName',
                'text',
                [
                    'required' => false,
                    'label' => 'Last Name',
                ]
            )
            ->add(
                'ssnLast4',
                'text',
                [
                    'required' => false,
                    'label' => 'SSN Last 4 digits',
                ]
            )
            ->add(
                'tosAcceptance',
                'checkbox',
                [
                    'label' => 'Term of use',
                    'required' => false,
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