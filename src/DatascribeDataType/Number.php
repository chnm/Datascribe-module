<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\Validator\ValidatorChain;

class Number implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Number'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Text('label');
        $element->setLabel('Number input label'); // @translate
        $element->setAttribute('value', $fieldData['label'] ?? null);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalNumber('min');
        $element->setLabel('Minimum value'); // @translate
        $element->setOption('info', 'The minimum value to accept for this input.'); // @translate
        $element->setAttribute('value', $fieldData['min'] ?? null);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalNumber('max');
        $element->setLabel('Maximum value'); // @translate
        $element->setOption('info', 'The maximum value to accept for this input.'); // @translate
        $element->setAttribute('value', $fieldData['max'] ?? null);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalNumber('step');
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

        $element = new DatascribeElement\OptionalNumber('default_value');
        $element->setLabel('Default value'); // @translate
        $element->setAttribute('value', $fieldData['default_value'] ?? null);
        $fieldset->add($element);

        $element = new Element\Textarea('datalist');
        $element->setLabel('Datalist'); // @translate
        $element->setOption('info', 'Recommended options available to choose, separated by new lines.'); // @translate
        $element->setAttribute('rows', 10);
        $element->setValue(implode("\n", $fieldData['datalist'] ?? []));
        $fieldset->add($element);
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        $fieldData = [];
        $fieldData['min'] =
            (isset($userData['min']) && is_numeric($userData['min']))
            ? $userData['min'] : null;
        $fieldData['max'] =
            (isset($userData['max']) && is_numeric($userData['max']))
            ? $userData['max'] : null;
        $fieldData['step'] =
            (isset($userData['step']) && is_numeric($userData['step']))
            ? $userData['step'] : null;
        $fieldData['placeholder'] =
            (isset($userData['placeholder']) && preg_match('/^.+$/', $userData['placeholder']))
            ? $userData['placeholder'] : null;
        $fieldData['pattern'] =
            (isset($userData['pattern']) && (false !== @preg_match(sprintf('/%s/', $userData['pattern']), '')))
            ? $userData['pattern'] : null;
        $fieldData['default_value'] =
            (isset($userData['default_value']) && is_numeric($userData['default_value']))
            ? $userData['default_value'] : null;
        $fieldData['label'] =
            (isset($userData['label']) && preg_match('/^.+$/', $userData['label']))
            ? $userData['label'] : null;
        if (isset($userData['datalist'])) {
            // The user data is usually a new line-delimited string, but it is
            // an array if it originates from form import.
            if (is_array($userData['datalist'])) {
                $options = $userData['datalist'];
            } elseif (preg_match('/^.+$/s', $userData['datalist'])) {
                $options = explode("\n", $userData['datalist']);
            } else {
                $options = [];
            }
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
        $element = new DatascribeElement\Number('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['label'] ?? 'Number'); // @translate
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
        if (isset($userData['value']) && is_numeric($userData['value'])) {
            $text = $userData['value'];
        }
        return $text;
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        $element = new DatascribeElement\Number('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $validatorChain = new ValidatorChain;
        foreach ($element->getValidators() as $validator) {
            $validatorChain->attach($validator);
        }
        return isset($valueText) ? $validatorChain->isValid($valueText) : false;
    }
}
