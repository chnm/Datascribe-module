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

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
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

    public function getFieldDataFromUserData(array $userData) : array
    {
        $fieldData = $userData['field_data'] ?? null;
        $fieldData = json_decode($fieldData, true);
        return is_array($fieldData) ? $fieldData : [];
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        return true;
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $element = new Element\Text('data_type');
        $element->setLabel('Unknown data type'); // @translate
        $element->setValue($this->name);
        $element->setAttribute('disabled', true);
        $fieldset->add($element);

        $element = new Element\Textarea('value_text_disabled');
        $element->setLabel('Value text'); // @translate
        $element->setValue($valueText);
        $element->setAttributes([
            'disabled' => true,
            'rows' => 8,
        ]);
        $fieldset->add($element);

        $element = new Element\Hidden('value_text');
        $element->setValue($valueText);
        $fieldset->add($element);
    }

    public function getValueTextFromUserData(array $userData) : string
    {
        return $userData['value_text'] ?? null;
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        return true;
    }
}
