<?php
namespace Datascribe\Service\Controller\Admin;

use Interop\Container\ContainerInterface;
use Datascribe\Controller\Admin\DatasetController;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DatasetControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new DatasetController(
            $services->get('Omeka\EntityManager')
        );
    }
}
