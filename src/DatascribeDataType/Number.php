<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator\ValidatorChain;

class Number implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Number'; // @translate
    }

    public function addFieldDataElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Text('min');
        $element->setLabel('Minimum value'); // @translate
        $element->setOption('info', 'The minimum value to accept for this input.'); // @translate
        $element->setAttribute('value', $fieldData['min'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('max');
        $element->setLabel('Maximum value'); // @translate
        $element->setOption('info', 'The maximum value to accept for this input.'); // @translate
        $element->setAttribute('value', $fieldData['max'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('step');
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

        $element = new Element\Text('number_input_label');
        $element->setLabel('Number input label'); // @translate
        $element->setAttribute('value', $fieldData['number_input_label'] ?? null);
        $fieldset->add($element);
    }

    public function getFieldData(array $fieldFormData) : array
    {
        $fieldData = [];
        $fieldData['min'] =
            (isset($fieldFormData['min']) && is_numeric($fieldFormData['min']))
            ? $fieldFormData['min'] : null;
        $fieldData['max'] =
            (isset($fieldFormData['max']) && is_numeric($fieldFormData['max']))
            ? $fieldFormData['max'] : null;
        $fieldData['step'] =
            (isset($fieldFormData['step']) && is_numeric($fieldFormData['step']))
            ? $fieldFormData['step'] : null;
        $fieldData['placeholder'] =
            (isset($fieldFormData['placeholder']) && preg_match('/^.+$/', $fieldFormData['placeholder']))
            ? $fieldFormData['placeholder'] : null;
        $fieldData['pattern'] =
            (isset($fieldFormData['pattern']) && preg_match('/^.+$/', $fieldFormData['pattern']))
            ? $fieldFormData['pattern'] : null;
        $fieldData['default_value'] =
            (isset($fieldFormData['default_value']) && is_numeric($fieldFormData['default_value']))
            ? $fieldFormData['default_value'] : null;
        $fieldData['number_input_label'] =
            (isset($fieldFormData['number_input_label']) && preg_match('/^.+$/', $fieldFormData['number_input_label']))
            ? $fieldFormData['number_input_label'] : null;
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldData().
        return true;
    }

    public function addValueDataElements(Fieldset $fieldset, string $fieldName, ?string $fieldDescription, array $fieldData, array $valueData) : void
    {
        $element = new DatascribeElement\Number('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['number_input_label'] ?? 'Number'); // @translate
        $value = null;
        if (isset($valueData['value'])) {
            $value = $valueData['value'];
        } elseif (isset($fieldData['default_value'])) {
            $value = $fieldData['default_value'];
        }
        $element->setValue($value);
        $fieldset->add($element);
    }

    public function getValueData(array $valueFormData) : array
    {
        $valueData = [];
        $valueData['value'] = $valueFormData['value'] ?? null;
        return $valueData;
    }

    public function valueDataIsValid(array $fieldData, array $valueData) : bool
    {
        $element = new DatascribeElement\Text('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $validatorChain = new ValidatorChain;
        foreach ($element->getValidators() as $validator) {
            $validatorChain->attach($validator);
        }
        return isset($valueData['value'])
            ? $validatorChain->isValid($valueData['value']) : false;
    }

    public function getHtml(array $valueData) : string
    {
    }

    public function getValue(array $valueData) : string
    {
        return $valueData['value'];
    }
}
