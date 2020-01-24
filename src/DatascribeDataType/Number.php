<?php
namespace Datascribe\DatascribeDataType;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;

class Number implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Number'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Number('min');
        $element->setLabel('Minimum value'); // @translate
        $element->setOption('info', 'The minimum value to accept for this input.'); // @translate
        $element->setAttribute('value', $fieldData['min'] ?? null);
        $fieldset->add($element);

        $element = new Element\Number('max');
        $element->setLabel('Maximum value'); // @translate
        $element->setOption('info', 'The maximum value to accept for this input.'); // @translate
        $element->setAttribute('value', $fieldData['max'] ?? null);
        $fieldset->add($element);

        $element = new Element\Number('step');
        $element->setLabel('Stepping interval'); // @translate
        $element->setOption('info', 'A number that specifies the granularity that the value must adhere to.'); // @translate
        $element->setAttribute('value', $fieldData['step'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('placeholder');
        $element->setLabel('Placeholder'); // @translate
        $element->setOption('info', 'An exemplar value to display in the input field whenever it is empty.'); // @translate
        $element->setAttribute('value', $fieldData['placeholder'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('pattern');
        $element->setLabel('Regex pattern'); // @translate
        $element->setOption('info', 'A regular expression the input\'s contents must match in order to be valid.'); // @translate
        $element->setAttribute('value', $fieldData['pattern'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('default_value');
        $element->setLabel('Default value'); // @translate
        $element->setAttribute('value', $fieldData['default_value'] ?? null);
        $fieldset->add($element);
    }

    public function getFieldData(array $fieldFormData) : array
    {
        $fieldData = [];
        $fieldData['min'] =
            (isset($fieldFormData['min']) && preg_match('/^\d+$/', $fieldFormData['min']))
            ? $fieldFormData['min'] : null;
        $fieldData['max'] =
            (isset($fieldFormData['max']) && preg_match('/^\d+$/', $fieldFormData['max']))
            ? $fieldFormData['max'] : null;
        $fieldData['placeholder'] =
            (isset($fieldFormData['placeholder']) && preg_match('/^.+$/', $fieldFormData['placeholder']))
            ? $fieldFormData['placeholder'] : null;
        $fieldData['pattern'] =
            (isset($fieldFormData['pattern']) && preg_match('/^.+$/', $fieldFormData['pattern']))
            ? $fieldFormData['pattern'] : null;
        $fieldData['default_value'] =
            (isset($fieldFormData['default_value']) && preg_match('/^.+$/', $fieldFormData['default_value']))
            ? $fieldFormData['default_value'] : null;
        return $fieldData;
    }

    public function getValueDataElement(array $fieldData, array $valueData) : Element
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
