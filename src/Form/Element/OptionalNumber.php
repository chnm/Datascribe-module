<?php
namespace Datascribe\Form\Element;

use Laminas\Form\Element\Number;
use Laminas\InputFilter\InputProviderInterface;

/**
 * By default, ZF3 sets Number elements as required. This makes it optional.
 */
class OptionalNumber extends Number implements InputProviderInterface
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
