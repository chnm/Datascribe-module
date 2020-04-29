<?php
namespace Datascribe\DatascribeDataType;

use Zend\Form\Element;
use Zend\Form\Fieldset;

abstract class AbstractSelection implements DataTypeInterface
{
    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Textarea('options');
        $element->setLabel('Options'); // @translate
        $element->setOption('info', 'The selection options separated by new lines.'); // @translate
        $element->setAttribute('rows', 10);
        $element->setValue(implode("\n", $fieldData['options'] ?? []));
        $fieldset->add($element);

        $element = new Element\Text('default_value');
        $element->setLabel('Default value'); // @translate
        $element->setValue($fieldData['default_value'] ?? null);
        $fieldset->add($element);

        $element = new Element\Text('field_label');
        $element->setLabel('Field label'); // @translate
        $element->setValue($fieldData['field_label'] ?? null);
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
        $fieldData['field_label'] =
            (isset($userData['field_label']) && preg_match('/^.+$/', $userData['field_label']))
            ? $userData['field_label'] : null;
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldDataFromUserData().
        return true;
    }

    public function getValueTextFromUserData(array $userData) : ?string
    {
        $text = null;
        if (isset($userData['value']) && is_string($userData['value']) && ('' !== $userData['value'])) {
            $text = $userData['value'];
        }
        return $text;
    }
}
