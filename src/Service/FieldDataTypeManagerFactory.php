<?php
namespace Datascribe\Service;

use Datascribe\FieldDataType\Manager;
use Omeka\Service\Exception;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class FieldDataTypeManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $config = $services->get('Config');
        if (!isset($config['datascribe_field_data_types'])) {
            throw new Exception\ConfigException('Missing datascribe_field_data_types configuration');
        }
        return new Manager($services, $config['datascribe_field_data_types']);
    }
}
