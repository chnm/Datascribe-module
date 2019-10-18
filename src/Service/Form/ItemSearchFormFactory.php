<?php
namespace Datascribe\Service\Form;

use Datascribe\Form\ItemSearchForm;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ItemSearchFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new ItemSearchForm($requestedName, $options);
        $form->setEntityManager($services->get('Omeka\EntityManager'));
        return $form;
    }
}
