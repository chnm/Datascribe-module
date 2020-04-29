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
        $formElement->addClass(DatascribeElement\Text::class, 'datascribeFormText');
        $formElement->addClass(DatascribeElement\Textarea::class, 'datascribeFormTextarea');
        $formElement->addClass(DatascribeElement\Number::class, 'datascribeFormNumber');
        $formElement->addClass(DatascribeElement\Checkbox::class, 'datascribeFormCheckbox');
        return $formElement;
    }
}
