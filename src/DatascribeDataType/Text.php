<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator\ValidatorChain;

class Text implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Text'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
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

        $element = new Element\Text('text_input_label');
        $element->setLabel('Text input label'); // @translate
        $element->setAttribute('value', $fieldData['text_input_label'] ?? null);
        $fieldset->add($element);

        $element = new Element\Textarea('datalist');
        $element->setLabel('Datalist'); // @translate
        $element->setAttribute('rows', 10);
        $element->setValue(implode("\n", $fieldData['datalist'] ?? []));
        $fieldset->add($element);
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        $fieldData = [];
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
        $fieldData['text_input_label'] =
            (isset($userData['text_input_label']) && preg_match('/^.+$/', $userData['text_input_label']))
            ? $userData['text_input_label'] : null;
        if (isset($userData['datalist']) && preg_match('/^.+$/s', $userData['datalist'])) {
            $options = explode("\n", $userData['datalist']);
            $options = array_map('trim', $options);
            $options = array_filter($options);
            $options = array_unique($options);
            $fieldData['datalist'] = $options;
        } else {
            $fieldData['datalist'] = [];
        }
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldDataFromUserData().
        return true;
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $element = new DatascribeElement\Text('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['text_input_label'] ?? 'Text'); // @translate
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
        return $userData['value'] ?? null;
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
