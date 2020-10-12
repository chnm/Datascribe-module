<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Laminas\Form\Fieldset;
use Laminas\Validator\ValidatorChain;

class Select extends AbstractSelection
{
    public function getLabel() : string
    {
        return 'Select'; // @translate
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $element = new DatascribeElement\Select('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['label'] ?? 'Select'); // @translate
        $element->setAttribute('class', 'chosen-select');
        $element->setAttribute('data-placeholder', '[No selection]'); // @translate
        $value = '';
        if (isset($valueText)) {
            $value = $valueText;
        } elseif (isset($fieldData['default_value'])) {
            $value = $fieldData['default_value'];
        }
        $element->setValue($value);
        $fieldset->add($element);
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
