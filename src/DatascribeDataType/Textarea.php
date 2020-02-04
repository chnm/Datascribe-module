<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator\ValidatorChain;

class Textarea implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Textarea'; // @translate
    }

    public function addFieldDataElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new DatascribeElement\OptionalNumber('rows');
        $element->setLabel('Rows'); // @translate
        $element->setAttribute('min', 1);
        $element->setAttribute('value', $fieldData['rows'] ?? null);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalNumber('minlength');
        $element->setLabel('Minimum length'); // @translate
        $element->setOption('info', 'The minimum number of characters long the input can be and still be considered valid.'); // @translate
        $element->setAttribute('min', 1);
        $element->setAttribute('value', $fieldData['minlength'] ?? null);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalNumber('maxlength');
        $element->setLabel('Maximum length'); // @translate
        $element->setOption('info', 'The maximum number of characters the input should accept.'); // @translate
        $element->setAttribute('min', 1);
        $element->setAttribute('value', $fieldData['maxlength'] ?? null);
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

        $element = new Element\Text('textarea_label');
        $element->setLabel('Textarea label'); // @translate
        $element->setAttribute('value', $fieldData['textarea_label'] ?? null);
        $fieldset->add($element);
    }

    public function getFieldData(array $fieldFormData) : array
    {
        $fieldData = [];
        $fieldData['rows'] =
            (isset($fieldFormData['rows']) && preg_match('/^\d+$/', $fieldFormData['rows']))
            ? $fieldFormData['rows'] : null;
        $fieldData['minlength'] =
            (isset($fieldFormData['minlength']) && preg_match('/^\d+$/', $fieldFormData['minlength']))
            ? $fieldFormData['minlength'] : null;
        $fieldData['maxlength'] =
            (isset($fieldFormData['maxlength']) && preg_match('/^\d+$/', $fieldFormData['maxlength']))
            ? $fieldFormData['maxlength'] : null;
        $fieldData['placeholder'] =
            (isset($fieldFormData['placeholder']) && preg_match('/^.+$/', $fieldFormData['placeholder']))
            ? $fieldFormData['placeholder'] : null;
        $fieldData['pattern'] =
            (isset($fieldFormData['pattern']) && preg_match('/^.+$/', $fieldFormData['pattern']))
            ? $fieldFormData['pattern'] : null;
        $fieldData['default_value'] =
            (isset($fieldFormData['default_value']) && preg_match('/^.+$/', $fieldFormData['default_value']))
            ? $fieldFormData['default_value'] : null;
        $fieldData['textarea_label'] =
            (isset($fieldFormData['textarea_label']) && preg_match('/^.+$/', $fieldFormData['textarea_label']))
            ? $fieldFormData['textarea_label'] : null;
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldData().
        return true;
    }

    public function addValueDataElements(Fieldset $fieldset, string $fieldName, ?string $fieldDescription, array $fieldData, array $valueData) : void
    {
        $element = new DatascribeElement\Textarea('text', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['textarea_label'] ?? 'Text'); // @translate
        $value = null;
        if (isset($valueData['text'])) {
            $value = $valueData['text'];
        } elseif (isset($fieldData['default_value'])) {
            $value = $fieldData['default_value'];
        }
        $element->setValue($value);
        $fieldset->add($element);
    }

    public function getValueData(array $valueFormData) : array
    {
        $valueData = [];
        $valueData['text'] = $valueFormData['text'] ?? null;
        return $valueData;
    }

    public function valueDataIsValid(array $fieldData, array $valueData) : bool
    {
        $element = new DatascribeElement\Text('text', [
            'datascribe_field_data' => $fieldData,
        ]);
        $validatorChain = new ValidatorChain;
        foreach ($element->getValidators() as $validator) {
            $validatorChain->attach($validator);
        }
        return $validatorChain->isValid($valueData['text']);
    }

    public function getHtml(array $valueData) : string
    {
    }

    public function getValue(array $valueData) : string
    {
        return $valueData['text'];
    }
}
