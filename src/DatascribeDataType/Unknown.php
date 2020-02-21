<?php
namespace Datascribe\DatascribeDataType;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;

class Unknown implements DataTypeInterface
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getLabel() : string
    {
        return '[Unknown]'; // @translate
    }

    public function addFieldDataElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Text('data_type');
        $element->setLabel('Unknown data type'); // @translate
        $element->setValue($this->name);
        $element->setAttribute('disabled', true);
        $fieldset->add($element);

        $element = new Element\Textarea('field_data_disabled');
        $element->setLabel('Field data'); // @translate
        $element->setValue(json_encode($fieldData, JSON_PRETTY_PRINT));
        $element->setAttributes([
            'disabled' => true,
            'rows' => 8,
        ]);
        $fieldset->add($element);

        $element = new Element\Hidden('field_data');
        $element->setValue(json_encode($fieldData));
        $fieldset->add($element);
    }

    public function getFieldData(array $fieldFormData) : array
    {
        $fieldData = $fieldFormData['field_data'] ?? null;
        $fieldData = json_decode($fieldData, true);
        return is_array($fieldData) ? $fieldData : [];
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        return true;
    }

    public function addValueDataElements(Fieldset $fieldset, array $fieldData, array $valueData) : void
    {
        $element = new Element\Text('data_type');
        $element->setLabel('Unknown data type'); // @translate
        $element->setValue($this->name);
        $element->setAttribute('disabled', true);
        $fieldset->add($element);

        $element = new Element\Textarea('value_data_disabled');
        $element->setLabel('Value data'); // @translate
        $element->setValue(json_encode($valueData, JSON_PRETTY_PRINT));
        $element->setAttributes([
            'disabled' => true,
            'rows' => 8,
        ]);
        $fieldset->add($element);

        $element = new Element\Hidden('value_data');
        $element->setValue(json_encode($valueData));
        $fieldset->add($element);
    }

    public function getValueData(array $valueFormData) : array
    {
        $valueData = $valueFormData['value_data'] ?? null;
        $valueData = json_decode($valueData, true);
        return is_array($valueData) ? $valueData : [];
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
