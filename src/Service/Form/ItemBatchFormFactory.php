<?php
namespace Datascribe\Service\Form;

use Datascribe\Form\ItemBatchForm;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ItemBatchFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new ItemBatchForm(null, $options);
        $form->setEntityManager($services->get('Omeka\EntityManager'));
        return $form;
    }
}
