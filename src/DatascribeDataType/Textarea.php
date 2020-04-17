<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\ValidatorChain;

class Textarea implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Textarea'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
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

    public function getFieldDataFromUserData(array $userData) : array
    {
        $fieldData = [];
        $fieldData['rows'] =
            (isset($userData['rows']) && preg_match('/^\d+$/', $userData['rows']))
            ? $userData['rows'] : null;
        $fieldData['minlength'] =
            (isset($userData['minlength']) && preg_match('/^\d+$/', $userData['minlength']))
            ? $userData['minlength'] : null;
        $fieldData['maxlength'] =
            (isset($userData['maxlength']) && preg_match('/^\d+$/', $userData['maxlength']))
            ? $userData['maxlength'] : null;
        $fieldData['placeholder'] =
            (isset($userData['placeholder']) && preg_match('/^.+$/', $userData['placeholder']))
            ? $userData['placeholder'] : null;
        $fieldData['pattern'] =
            (isset($userData['pattern']) && preg_match('/^.+$/', $userData['pattern']))
            ? $userData['pattern'] : null;
        $fieldData['default_value'] =
            (isset($userData['default_value']) && preg_match('/^.+$/', $userData['default_value']))
            ? $userData['default_value'] : null;
        $fieldData['textarea_label'] =
            (isset($userData['textarea_label']) && preg_match('/^.+$/', $userData['textarea_label']))
            ? $userData['textarea_label'] : null;
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldDataFromUserData().
        return true;
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $element = new DatascribeElement\Textarea('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['textarea_label'] ?? 'Text'); // @translate
        $value = null;
        if (isset($valueText)) {
            $value = $valueText;
        } elseif (isset($fieldData['default_value'])) {
            $value = $fieldData['default_value'];
        }
        $element->setValue($value);
        $fieldset->add($element);
    }

    public function getValueTextFromUserData(array $userData) : ?string
    {
        $text = null;
        if (isset($userData['value']) && is_string($userData['value']) && ('' !== $userData['value'])) {
            $text = $userData['value'];
        }
        return $text;
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        $element = new DatascribeElement\Text('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $validatorChain = new ValidatorChain;
        foreach ($element->getValidators() as $validator) {
            $validatorChain->attach($validator);
        }
        return isset($valueText) ? $validatorChain->isValid($valueText) : false;
    }
}
