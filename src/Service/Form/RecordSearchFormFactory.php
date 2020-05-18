<?php
namespace Datascribe\Service\Form;

use Datascribe\Form\RecordSearchForm;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class RecordSearchFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new RecordSearchForm(null, $options);
        $form->setEntityManager($services->get('Omeka\EntityManager'));
        return $form;
    }
}
