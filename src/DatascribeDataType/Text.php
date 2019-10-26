<?php
namespace Datascribe\DatascribeDataType;

use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;

class Text implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'Text';
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new Element\Radio('type');
        $element->setLabel('Input type');
        $element->setValueOptions([
           'text_input' => 'Single line',
           'textarea' => 'Multiline',
        ]);
        $element->setAttribute('required', true);
        $fieldset->add($element);

        $element = new Element\Number('rows');
        $element->setLabel('Rows (multiline only)');
        $element->setAttribute('min', 1);
        $fieldset->add($element);

        $element = new Element\Number('maxlength');
        $element->setLabel('Maximum input length');
        $element->setOption('info', 'The maximum number of characters the input should accept.');
        $element->setAttribute('min', 1);
        $fieldset->add($element);

        $element = new Element\Number('minlength');
        $element->setLabel('Minimum input length');
        $element->setOption('info', 'The minimum number of characters long the input can be and still be considered valid.');
        $element->setAttribute('min', 1);
        $fieldset->add($element);

        $element = new Element\Text('placeholder');
        $element->setLabel('Placeholder');
        $element->setOption('info', 'An exemplar value to display in the input field whenever it is empty.');
        $fieldset->add($element);

        $element = new Element\Text('pattern');
        $element->setLabel('Regex pattern');
        $element->setOption('info', 'A regular expression the input\'s contents must match in order to be valid.');
        $fieldset->add($element);

        $element = new Element\Text('default_value');
        $element->setLabel('Default value');
        $fieldset->add($element);
    }

    public function getFieldData(array $fieldFormData) : array
    {
        return [];
    }

    public function addValueElements(Fieldset $fieldset, array $valueData) : void
    {
    }

    public function getValueData(array $valueFormData) : array
    {
    }

    public function getHtml(array $valueData) : string
    {
    }

    public function getValue(array $valueData) : string
    {
    }
}
