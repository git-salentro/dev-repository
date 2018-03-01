<?php

namespace Erp\StripeBundle\Form\Type;

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
                'dateFrom',
                'date',
                [
                    'label' => 'Date From',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => ['placeholder'=>'Date From']
                ]
            )
            ->add(
                'dateTo',
                'date',
                [
                    'label' => 'Date To',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => ['placeholder'=>'Date To']
                ]
            )
            ->add(
                'landlord',
                'entity',
                [
                    'class' => User::class,
                    'query_builder' => function (EntityRepository $repository) {
                        $user = $this->tokenStorage->getToken()->getUser();
                        $qb = $repository->createQueryBuilder('u')
                            ->where('u.manager = :manager')
                            ->setParameter('manager', $user);

                        return $qb;
                    },
                    'label' => 'Landlord',
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
        ]);
    }
}