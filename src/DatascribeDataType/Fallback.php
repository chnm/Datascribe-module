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

    public function addFieldDataElements(Fieldset $fieldset, array $fieldData) : void
    {
    }

    public function getFieldData(array $fieldFormData) : array
    {
        return [];
    }

    public function addValueDataElements(Fieldset $fieldset, string $fieldName, ?string $fieldDescription, array $fieldData, array $valueData) : void
    {
    }

    public function getValueData(array $valueFormData) : array
    {
        return [];
    }

    public function valueDataIsValid(array $fieldData, array $valueData) : bool
    {
        return true;
    }

    public function getHtml(array $valueData) : string
    {
        return '';
    }

    public function getValue(array $valueData) : string
    {
        return '';
    }
}
