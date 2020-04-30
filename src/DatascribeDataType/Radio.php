<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Validator\ValidatorChain;

class Radio extends AbstractSelection
{
    public function getLabel() : string
    {
        return 'Radio'; // @translate
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $element = new DatascribeElement\Radio('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel($fieldData['label'] ?? 'Select'); // @translate
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
        $element = new DatascribeElement\Radio('value', [
            'datascribe_field_data' => $fieldData,
        ]);
        $validatorChain = new ValidatorChain;
        foreach ($element->getValidators() as $validator) {
            $validatorChain->attach($validator);
        }
        return isset($valueText) ? $validatorChain->isValid($valueText) : false;
    }
}
