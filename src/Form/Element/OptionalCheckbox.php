<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Checkbox;
use Zend\InputFilter\InputProviderInterface;

/**
 * By default, ZF3 sets Checkbox elements as required. This makes it optional.
 */
class OptionalCheckbox extends Checkbox implements InputProviderInterface
{
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => false,
            'validators' => [],
            'filters' => [],
        ];
    }
}
