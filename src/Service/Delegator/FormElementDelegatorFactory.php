<?php
namespace Datascribe\Service\Delegator;

use Datascribe\Form\Element as DatascribeElement;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

class FormElementDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name,
        callable $callback, array $options = null
    ) {
        $formElement = $callback();
        //~ $formElement->addClass(DatascribeElement\Text::class, 'formText');
        $formElement->addClass(DatascribeElement\Textarea::class, 'datascribeFormTextarea');
        return $formElement;
    }
}
