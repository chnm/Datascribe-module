<?php
namespace Datascribe\Service;

use Datascribe\DatascribeDataType\Manager;
use Omeka\Service\Exception;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class DataTypeManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $config = $services->get('Config');
        if (!isset($config['datascribe_data_types'])) {
            throw new Exception\ConfigException('Missing datascribe_data_types configuration');
        }
        return new Manager($services, $config['datascribe_data_types']);
    }
}
