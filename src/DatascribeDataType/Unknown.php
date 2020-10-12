<?php
namespace Datascribe\DatascribeDataType;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;

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

        $element = new Element\Textarea('field_data');
        $element->setLabel('Field data'); // @translate
        $element->setValue(json_encode($fieldData, JSON_PRETTY_PRINT));
        $element->setAttributes([
            'disabled' => true,
            'rows' => 8,
        ]);
        $fieldset->add($element);

        // We need to add an unused element here so the "data" fieldset appears
        // in POST data. Otherwise the API request will not validate.
        $fieldset->add(new Element\Hidden('unknown'));
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        return [];
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

        $element = new Element\Textarea('value_text');
        $element->setLabel('Value text'); // @translate
        $element->setValue($valueText);
        $element->setAttributes([
            'disabled' => true,
            'rows' => 8,
        ]);
        $fieldset->add($element);
    }

    public function getValueTextFromUserData(array $userData) : ?string
    {
        return null;
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        // Note that we assume all "Unknown" text is valid so it persists until
        // an administrator corrects or deletes the field.
        return true;
    }
}
