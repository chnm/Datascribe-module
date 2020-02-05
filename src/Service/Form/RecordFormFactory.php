<?php
namespace Datascribe\Service\Form;

use Datascribe\Form\RecordForm;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class RecordFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new RecordForm(null, $options);
        $form->setDataTypeManager($services->get('Datascribe\DataTypeManager'));
        return $form;
    }
}
