<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\ValidatorChain;

class Checkbox implements DataTypeInterface
{
    const DEFAULT_CHECKED_VALUE = '1';
    const DEFAULT_UNCHECKED_VALUE = '0';

    public function getLabel() : string
    {
        return 'Checkbox'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Text('checked_value');
        $element->setLabel('Checked value'); // @translate
        $element->setAttribute('value', $fieldData['checked_value'] ?? self::DEFAULT_CHECKED_VALUE);
        $fieldset->add($element);

        $element = new Element\Text('unchecked_value');
        $element->setLabel('Unhecked value'); // @translate
        $element->setAttribute('value', $fieldData['unchecked_value'] ?? self::DEFAULT_UNCHECKED_VALUE);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalCheckbox('checked_by_default');
        $element->setLabel('Checked by default'); // @translate
        $element->setValue($fieldData['checked_by_default'] ?? '0');
        $fieldset->add($element);

        $element = new Element\Text('checkbox_label');
        $element->setLabel('Checkbox label'); // @translate
        $element->setAttribute('value', $fieldData['checkbox_label'] ?? null);
        $fieldset->add($element);
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        $fieldData = [];
        $fieldData['checked_value'] =
            (isset($userData['checked_value']) && preg_match('/^.+$/', $userData['checked_value']))
            ? $userData['checked_value'] : self::DEFAULT_CHECKED_VALUE;
        $fieldData['unchecked_value'] =
            (isset($userData['unchecked_value']) && preg_match('/^.+$/', $userData['unchecked_value']))
            ? $userData['unchecked_value'] : self::DEFAULT_UNCHECKED_VALUE;
        $fieldData['checked_by_default'] =
            (isset($userData['checked_by_default']) && in_array($userData['checked_by_default'], ['0', '1']))
            ? $userData['checked_by_default'] : '0';
        $fieldData['checkbox_label'] =
            (isset($userData['checkbox_label']) && preg_match('/^.+$/', $userData['checkbox_label']))
            ? $userData['checkbox_label'] : null;
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        if ($fieldData['checked_value'] === $fieldData['unchecked_value']) {
            return false;
        }
        return true;
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $element = new Element\Checkbox('value');
        $element->setLabel($fieldData['checkbox_label'] ?? 'Check'); // @translate
        $element->setUseHiddenElement(true);
        $element->setCheckedValue($fieldData['checked_value'] ?? self::DEFAULT_CHECKED_VALUE);
        $element->setUncheckedValue($fieldData['unchecked_value'] ?? self::DEFAULT_UNCHECKED_VALUE);
        if (is_null($valueText) && isset($fieldData['checked_by_default']) && '1' === $fieldData['checked_by_default']) {
            $valueText = $fieldData['checked_value'] ?? self::DEFAULT_CHECKED_VALUE;
        }
        $element->setValue($valueText);
        $fieldset->add($element);
    }

    public function getValueTextFromUserData(array $userData) : ?string
    {
        return $userData['value'] ?? null;
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        $checkedValue = $fieldData['checked_value'] ?? self::DEFAULT_CHECKED_VALUE;
        $uncheckedValue = $fieldData['unchecked_value'] ?? self::DEFAULT_UNCHECKED_VALUE;
        return in_array($valueText, [$checkedValue, $uncheckedValue]);
    }
}
