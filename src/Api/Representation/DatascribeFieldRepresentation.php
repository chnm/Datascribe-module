<?php
namespace Datascribe\Api\Representation;

use Datascribe\DatascribeDataType\Unknown;
use Datascribe\Entity\DatascribeField;
use Omeka\Api\Representation\AbstractRepresentation;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatascribeFieldRepresentation extends AbstractRepresentation
{
    /**
     * @var DatascribeField
     */
    protected $field;

    /**
     * @param DatascribeField $field
     * @param ServiceLocatorInterface $services
     */
    public function __construct(DatascribeField $field, ServiceLocatorInterface $services)
    {
        $this->setServiceLocator($services);
        $this->field = $field;
    }

    public function jsonSerialize()
    {
    }

    public function id()
    {
        return $this->field->getId();
    }

    public function name(array $options = [])
    {
        // Set default options.
        $options['length'] = $options['length'] ?? null;
        $options['trim_marker'] = $options['trim_marker'] ?? null;

        $name = $this->field->getName();
        $nameLength = mb_strlen($name);
        if ($options['length']) {
            $name = mb_substr($name, 0, (int) $options['length']);
        }
        if ($options['trim_marker'] && $nameLength > mb_strlen($name)) {
            $name .= $options['trim_marker'];
        }
        return $name;
    }

    public function description()
    {
        return $this->field->getDescription();
    }

    public function dataset()
    {
        return $this->getAdapter('datascribe_datasets')
            ->getRepresentation($this->resource->getDataset());
    }

    public function position()
    {
        return $this->field->getPosition();
    }

    public function isPrimary()
    {
        return $this->field->getIsPrimary();
    }

    public function isRequired()
    {
        return $this->field->getIsRequired();
    }

    public function dataType()
    {
        return $this->field->getDataType();
    }

    public function dataTypeService()
    {
        return $this->getServiceLocator()
            ->get('Datascribe\DataTypeManager')
            ->get($this->dataType());
    }

    public function dataTypeIsUnknown()
    {
        return ($this->dataTypeService() instanceof Unknown);
    }

    public function data()
    {
        return $this->field->getData();
    }
}
