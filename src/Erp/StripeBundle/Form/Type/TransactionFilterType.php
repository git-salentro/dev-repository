<?php

namespace Erp\StripeBundle\Form\Type;

use Erp\PropertyBundle\Entity\Property;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityRepository;

class TransactionFilterType extends AbstractFilterType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'landlord',
                'entity',
                [
                    'class' => User::class,
                    'label' => 'Landlord',
                    'empty_data' => null,
                    'property' => 'FullName',
                    'required' => false,
                    'query_builder' => function (EntityRepository $repository) {
                        $user = $this->tokenStorage->getToken()->getUser();
                        $qb = $repository->createQueryBuilder('u')
                            ->where('u.manager = :manager')
                            ->setParameter('manager', $user);
                        return $qb;
                    },

                ]
            )
            ->add(
                'dateFrom',
                'date',
                [
                    'required' => false,
                    'label' => 'Date From',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => [
                        'placeholder' => 'Date From',
                    ]
                ]
            )
            ->add(
                'dateTo',
                'date',
                [
                    'required' => false,
                    'label' => 'Date To',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => [
                        'placeholder' => 'Date To',
                    ]
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
