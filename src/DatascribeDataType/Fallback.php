<?php
namespace Datascribe\DatascribeDataType;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;

class Fallback implements DataTypeInterface
{
    public function getLabel() : string
    {
        return '[Unknown]'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
    }

    public function getFieldData(array $fieldFormData) : array
    {
        return [];
    }

    public function addValueElements(Fieldset $fieldset, array $valueData) : void
    {
    }

    public function getValueData(array $valueFormData) : array
    {
    }

    public function getHtml(array $valueData) : string
    {
    }

    public function getValue(array $valueData) : string
    {
    }
}
