<?php
namespace Datascribe\Api\Representation;

use Datascribe\Entity\DatascribeValue;
use Omeka\Api\Representation\AbstractRepresentation;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatascribeValueRepresentation extends AbstractRepresentation
{
    /**
     * @var DatascribeValue
     */
    protected $value;

    /**
     * @param DatascribeField $field
     * @param ServiceLocatorInterface $services
     */
    public function __construct(DatascribeValue $value, ServiceLocatorInterface $services)
    {
        $this->setServiceLocator($services);
        $this->value = $value;
    }

    public function jsonSerialize()
    {
    }

    public function id()
    {
        return $this->value->getId();
    }

    public function data()
    {
        return $this->value->getData();
    }

    public function field()
    {
        return new DatascribeFieldRepresentation(
            $this->value->getField(),
            $this->getServiceLocator()
        );
    }

    public function record()
    {
        return $this->getAdapter('datascribe_records')
            ->getRepresentation($this->value->getRecord());
    }

    public function isInvalid()
    {
        return $this->value->getIsInvalid();
    }

    public function isMissing()
    {
        return $this->value->getIsMissing();
    }

    public function isIllegible()
    {
        return $this->value->getIsIllegible();
    }

    public function value()
    {
        $manager = $this->getServiceLocator()->get('Datascribe\DataTypeManager');
        $dataType = $manager->get($this->field()->dataType());
        return $dataType->getValue($this->data());
    }
}
