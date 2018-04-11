<?php

namespace Erp\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class NotificationType extends AbstractType
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
                    'label' => 'Days Before Rent Due Date',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_notification_notification';
    }
}