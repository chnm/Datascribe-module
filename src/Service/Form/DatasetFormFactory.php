<?php
namespace Datascribe\Service\Form;

use Datascribe\Form\DatasetForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class DatasetFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new DatasetForm(null, $options);
        $form->setDataTypeManager($services->get('Datascribe\DataTypeManager'));
        $form->setViewHelperManager($services->get('ViewHelperManager'));
        return $form;
    }
}
