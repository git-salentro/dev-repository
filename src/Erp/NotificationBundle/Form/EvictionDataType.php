<?php

namespace Erp\NotificationBundle\Form;

use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Erp\NotificationBundle\Entity\Template;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class EvictionDataType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $templates = $options['templates'] ?? [];
        $builder
            ->add(
                'days',
                'number',
                    [
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Please enter the days after the due date for send eviction',
                                'groups' => ['Eviction']
                            ]
                        )
                    ],
                    'label' => 'Enter Days After Due Rent'
                    ]
                )
            ->add(
                'template',
                EntityType::class,
                [
                    'required' => true,
                    'class' => Template::class,
                    'property' => 'title',
                    'choices' => $templates,
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Please select template from drop-down',
                                'groups' => ['Eviction']
                            ]
                        )
                    ]
                ]
            )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Erp\NotificationBundle\Entity\EvictionData',
            'templates' => [],
            'validation_groups' => ['Eviction']
        ));
    }



    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_notificationbundle_evictiondata';
    }
}
