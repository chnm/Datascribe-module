<?php
namespace Datascribe\Service\ControllerPlugin;

use Interop\Container\ContainerInterface;
use Datascribe\ControllerPlugin\Datascribe;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DatascribeFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new Datascribe($services);
    }
}
