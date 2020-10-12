<?php
namespace Datascribe\Form\Element;

use Laminas\Form\Element\Checkbox;
use Laminas\InputFilter\InputProviderInterface;

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
