<?php
namespace Datascribe\DatascribeDataType;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;

abstract class AbstractSelection implements DataTypeInterface
{
    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Text('label');
        $element->setLabel('Selection label'); // @translate
        $element->setValue($fieldData['label'] ?? null);
        $fieldset->add($element);

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
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        $fieldData = [];
        if (isset($userData['options'])) {
            // The user data is usually a new line-delimited string, but it is
            // an array if it originates from form import.
            if (is_array($userData['options'])) {
                $options = $userData['options'];
            } elseif (preg_match('/^.+$/s', $userData['options'])) {
                $options = explode("\n", $userData['options']);
            } else {
                $options = [];
            }
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
        $fieldData['label'] =
            (isset($userData['label']) && preg_match('/^.+$/', $userData['label']))
            ? $userData['label'] : null;
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
