<?php

namespace Erp\NotificationBundle\Form\Type;

use Erp\NotificationBundle\Entity\UserNotification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserNotificationType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'notifications',
                CollectionType::class,
                [
                    'entry_type' => UserNotificationType::class,
                ]
            )
            ->add(
                'alerts',
                CollectionType::class,
                [
                    'entry_type' => AlertType::class,
                ]
            )
            ->add(
                'sendAlertAutomatically',
                CheckboxType::class,
                [
                    'label' => 'Automatically send Alert on Rent Due Date?',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Submit
                    '
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
