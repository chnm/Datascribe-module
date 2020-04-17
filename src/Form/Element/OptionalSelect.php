<?php
namespace Datascribe\Form\Element;

use Laminas\Form\Element\Select;
use Laminas\InputFilter\InputProviderInterface;

/**
 * By default, ZF3 sets Select elements as required. This makes it optional.
 */
class OptionalSelect extends Select implements InputProviderInterface
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
