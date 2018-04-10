<?php

namespace Erp\NotificationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\NotificationBundle\Entity\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Trsteel\CkeditorBundle\Form\Type\CkeditorType;

class TemplateType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                TextType::class,
                [
                    'label' => 'Type',
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Title',
                ]
            )
            ->add(
                'description',
                CkeditorType::class,
                [
                    'label' => 'Description',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
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
            'data_class' => Template::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_notification_template';
    }
}