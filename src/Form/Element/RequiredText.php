<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Text;
use Zend\InputFilter\InputProviderInterface;

/**
 * By default, ZF3 sets Text elements as optional. This makes it required.
 */
class RequiredText extends Text implements InputProviderInterface
{
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => true,
            'validators' => [],
            'filters' => [],
        ];
    }
}
