<?php
namespace Datascribe\Api\Representation;

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

    public function name()
    {
        return $this->field->getName();
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

    public function dataType()
    {
        return $this->field->getDataType();
    }

    public function data()
    {
        return $this->field->getData();
    }
}
