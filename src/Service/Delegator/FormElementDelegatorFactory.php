<?php
namespace Datascribe\Service\Delegator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class FormElementDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name,
        callable $callback, array $options = null
    ) {
        $formElement = $callback();
        $formElement->addClass(Datascribe\Form\Element\Text::class, 'formText');
        $formElement->addClass(Datascribe\Form\Element\Textarea::class, 'formTextarea');
        return $formElement;
    }
}
