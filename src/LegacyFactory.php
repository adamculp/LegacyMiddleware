<?php
namespace Legacy;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\Common\EventManager;

class LegacyFactory
{
    public function __invoke(
        ContainerInterface $container
        ) {
            $config = $container->get('config');
            $legacyDb = $container->get(LegacyDb\LegacyDbMysqli::class);
            $evm = new EventManager();

            return new Legacy(
                $config['legacy_routes'],
                $legacyDb,
                $evm
            );
    }
}
