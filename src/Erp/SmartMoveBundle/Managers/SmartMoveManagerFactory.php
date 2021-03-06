<?php

namespace Erp\SmartMoveBundle\Managers;

use Erp\SmartMoveBundle\Exceptions\SmartMoveManagerException;
use Erp\SmartMoveBundle\Managers\API\LandlordAPIManager;
use Erp\SmartMoveBundle\Managers\API\RenterAPIManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SmartMoveManagerFactory
{
    /**
     * Get Smart Move manager instance
     *
     * @param string             $type
     * @param ContainerInterface $container
     *
     * @return LandlordAPIManager|RenterAPIManager
     * @throws SmartMoveManagerException
     */
    public static function getInstance($type, ContainerInterface $container)
    {
        switch ($type) {
            case SmartMoveManagerInterface::TYPE_RENTER:
                $manager = new RenterAPIManager($container);
                break;
            case SmartMoveManagerInterface::TYPE_LANDLORD:
                $manager = new LandlordAPIManager($container);
                break;
            default:
                $available = [
                    SmartMoveManagerInterface::TYPE_RENTER,
                    SmartMoveManagerInterface::TYPE_LANDLORD
                ];
                throw new SmartMoveManagerException(
                    sprintf(
                        'SmartMOve manager %s not found. Available managers: %s',
                        $type,
                        implode(', ', $available)
                    )
                );
                break;
        }

        return $manager;
    }
}
