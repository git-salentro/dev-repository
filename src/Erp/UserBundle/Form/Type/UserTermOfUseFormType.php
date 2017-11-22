<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Doctrine\ORM\EntityRepository;

class UserTermOfUseFormType extends AbstractType
{
    /**
     * @var string
     */
    protected $validationGroup = '';

    /**
     * @var string
     */
    protected $translationDomain = 'FOSUserBundle';

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * Construct method
     */
    public function __construct()
    {
        $this->validationGroup = 'UserTermOfUse';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->addIsTermOfUse();

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Accept', 'attr' => ['class' => 'btn submit-popup-btn', 'disabled' => 'disabled']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\UserBundle\Entity\User',
                'validation_groups' => [$this->validationGroup],
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_users_term_of_use';
    }

    /**
     * @return $this
     */
    private function addIsTermOfUse()
    {
        $this->formBuilder->add(
            'isTermOfUse',
            'checkbox',
            ['required' => true]
        );

        return $this;
    }
}
