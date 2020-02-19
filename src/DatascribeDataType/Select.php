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

    public function addFieldDataElements(Fieldset $fieldset, array $fieldData) : void
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

    public function getFieldData(array $fieldFormData) : array
    {
        $fieldData = [];
        if (isset($fieldFormData['options']) && preg_match('/^.+$/s', $fieldFormData['options'])) {
            $options = explode("\n", $fieldFormData['options']);
            $options = array_map('trim', $options);
            $options = array_filter($options);
            $options = array_unique($options);
            $fieldData['options'] = $options;
        } else {
            $fieldData['options'] = [];
        }
        $fieldData['default_value'] =
            (isset($fieldFormData['default_value']) && preg_match('/^.+$/', $fieldFormData['default_value']))
            ? $fieldFormData['default_value'] : null;
        $fieldData['select_label'] =
            (isset($fieldFormData['select_label']) && preg_match('/^.+$/', $fieldFormData['select_label']))
            ? $fieldFormData['select_label'] : null;
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldData().
        return true;
    }

    public function addValueDataElements(Fieldset $fieldset, string $fieldName, ?string $fieldDescription, array $fieldData, array $valueData) : void
    {
        $element = new DatascribeElement\Select('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['select_label'] ?? 'Select'); // @translate
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
        $element = new DatascribeElement\Select('value', [
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
