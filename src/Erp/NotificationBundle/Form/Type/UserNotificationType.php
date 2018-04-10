<?php

namespace Erp\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\NotificationBundle\Entity\UserNotification;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserNotificationType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'daysBefore',
                TextType::class,
                [
                    'label' => 'Days Before Rent Due Date'
                ]
            )
            ->add(
                'daysAfter',
                TextType::class,
                [
                    'label' => 'Days After Rent Due Date'
                ]
            )
            ->add(
                'sendAlertAutomatically',
                CheckboxType::class,
                [
                    'label' => 'Automatically send Alert on Rent Due Date?'
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Days Before Rent Due Date'
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserNotification::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_notification_user_notification';
    }
}