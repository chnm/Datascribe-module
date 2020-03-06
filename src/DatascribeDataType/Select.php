<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator\ValidatorChain;

class Select implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Select'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Textarea('options');
        $element->setLabel('Options'); // @translate
        $element->setOption('info', 'The select options separated by new lines.'); // @translate
        $element->setAttribute('rows', 10);
        $element->setValue(implode("\n", $fieldData['options'] ?? []));
        $fieldset->add($element);

        $element = new Element\Text('default_value');
        $element->setLabel('Default value'); // @translate
        $element->setValue($fieldData['default_value'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('select_label');
        $element->setLabel('Select label'); // @translate
        $element->setValue($fieldData['select_label'] ?? null);
        $fieldset->add($element);
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        $fieldData = [];
        if (isset($userData['options']) && preg_match('/^.+$/s', $userData['options'])) {
            $options = explode("\n", $userData['options']);
            $options = array_map('trim', $options);
            $options = array_filter($options);
            $options = array_unique($options);
            $fieldData['options'] = $options;
        } else {
            $fieldData['options'] = [];
        }
        $fieldData['default_value'] =
            (isset($userData['default_value']) && preg_match('/^.+$/', $userData['default_value']))
            ? $userData['default_value'] : null;
        $fieldData['select_label'] =
            (isset($userData['select_label']) && preg_match('/^.+$/', $userData['select_label']))
            ? $userData['select_label'] : null;
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldDataFromUserData().
        return true;
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $element = new DatascribeElement\Select('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['select_label'] ?? 'Select'); // @translate
        $element->setAttribute('class', 'chosen-select');
        $element->setAttribute('data-placeholder', '[No selection]'); // @translate
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
        $element = new DatascribeElement\Select('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $validatorChain = new ValidatorChain;
        foreach ($element->getValidators() as $validator) {
            $validatorChain->attach($validator);
        }
        return isset($valueText) ? $validatorChain->isValid($valueText) : false;
    }
}
