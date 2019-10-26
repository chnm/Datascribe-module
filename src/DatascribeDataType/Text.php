<?php
namespace Datascribe\DatascribeDataType;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;

class Text implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Text';
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Radio('type');
        $element->setLabel('Input type');
        $element->setValueOptions([
           'text_input' => 'Single line',
           'textarea' => 'Multiline',
        ]);
        $element->setAttribute('value', $fieldData['type'] ?? 'text_input');
        $fieldset->add($element);

        $element = new Element\Number('rows');
        $element->setLabel('Rows (multiline only)');
        $element->setAttribute('min', 1);
        $element->setAttribute('value', $fieldData['rows'] ?? null);
        $fieldset->add($element);

        $element = new Element\Number('minlength');
        $element->setLabel('Minimum input length');
        $element->setOption('info', 'The minimum number of characters long the input can be and still be considered valid.');
        $element->setAttribute('min', 1);
        $element->setAttribute('value', $fieldData['minlength'] ?? null);
        $fieldset->add($element);

        $element = new Element\Number('maxlength');
        $element->setLabel('Maximum input length');
        $element->setOption('info', 'The maximum number of characters the input should accept.');
        $element->setAttribute('min', 1);
        $element->setAttribute('value', $fieldData['maxlength'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('placeholder');
        $element->setLabel('Placeholder');
        $element->setOption('info', 'An exemplar value to display in the input field whenever it is empty.');
        $element->setAttribute('value', $fieldData['placeholder'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('pattern');
        $element->setLabel('Regex pattern');
        $element->setOption('info', 'A regular expression the input\'s contents must match in order to be valid.');
        $element->setAttribute('value', $fieldData['pattern'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('default_value');
        $element->setLabel('Default value');
        $element->setAttribute('value', $fieldData['default_value'] ?? null);
        $fieldset->add($element);
    }

    public function getFieldData(array $fieldFormData) : array
    {
        $fieldData = [];
        $fieldData['type'] =
            (isset($fieldFormData['type']) && in_array($fieldFormData['type'], ['text_input', 'textarea']))
            ? $fieldFormData['type'] : 'text_input';
        $fieldData['rows'] =
            (isset($fieldFormData['rows']) && preg_match('/^\d+$/', $fieldFormData['rows']))
            ? $fieldFormData['rows'] : null;
        $fieldData['maxlength'] =
            (isset($fieldFormData['maxlength']) && preg_match('/^\d+$/', $fieldFormData['maxlength']))
            ? $fieldFormData['maxlength'] : null;
        $fieldData['minlength'] =
            (isset($fieldFormData['minlength']) && preg_match('/^\d+$/', $fieldFormData['minlength']))
            ? $fieldFormData['minlength'] : null;
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
