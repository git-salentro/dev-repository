<?php

namespace Erp\CoreBundle\Twig;

use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CoreExtention
 *
 * @package Erp\UserBundle\Twig
 */
class CoreExtention extends \Twig_Extension
{
    const DEFAULT_CURRENCY_LOCALE = 'en_US';
    const DEFAULT_CURRENCY = 'USD';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_extension';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            'json_decode' => new \Twig_Filter_Method($this, 'jsonDecode'),
            'money' => new \Twig_Filter_Method($this, 'formatAsMoney'),
        ];
    }

    public function formatAsMoney($value)
    {
        if (!is_int($value)) {
            return;
        }
        // TODO Refactoring
        $value = $value / 100;
        $format = new \NumberFormatter(self::DEFAULT_CURRENCY_LOCALE, \NumberFormatter::CURRENCY);

        return $format->formatCurrency($value, self::DEFAULT_CURRENCY);
    }

    /**
     * Decode string to array
     *
     * @param $str
     *
     * @return array
     */
    public function jsonDecode($str)
    {
        return json_decode($str, true);
    }
}
