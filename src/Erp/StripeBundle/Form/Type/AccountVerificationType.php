<?php

namespace Erp\StripeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Erp\PaymentBundle\Entity\StripeAccount;

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
                    'required' => true,
                    'label' => 'City',
                ]
            )
            ->add(
                'line1',
                'text',
                [
                    'required' => true,
                    'label' => 'Line 1',
                ]
            )
            ->add(
                'postalCode',
                'text',
                [
                    'required' => true,
                    'label' => 'Postal Code',
                ]
            )
            ->add(
                'state',
                'text',
                [
                    'required' => true,
                    'label' => 'State',
                ]
            )
            ->add(
                'businessName',
                'text',
                [
                    'required' => true,
                    'label' => 'Business Name',
                ]
            )
            ->add(
                'businessTaxId',
                'text',
                [
                    'required' => true,
                    'label' => 'Business Tax Id',
                ]
            )
            ->add(
                'dayOfBirth',
                'text',
                [
                    'required' => true,
                    'label' => 'Day Of Birth',
                ]
            )
            ->add(
                'monthOfBirth',
                'text',
                [
                    'required' => true,
                    'label' => 'Month Of Birth',
                ]
            )
            ->add(
                'yearOfBirth',
                'text',
                [
                    'required' => true,
                    'label' => 'Year Of Birth',
                ]
            )
            ->add(
                'firstName',
                'text',
                [
                    'required' => true,
                    'label' => 'First Name',
                ]
            )
            ->add(
                'lastName',
                'text',
                [
                    'required' => true,
                    'label' => 'Last Name',
                ]
            )
            ->add(
                'ssnLast4',
                'text',
                [
                    'required' => true,
                    'label' => 'SSN Last 4 digits',
                ]
            )
            ->add(
                'tosAcceptance',
                'checkbox',
                [
                    'label' => 'Term of use',
                    'required' => true,
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