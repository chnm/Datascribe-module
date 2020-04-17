<?php
namespace Datascribe\Service\Form;

use Datascribe\Form\ItemForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class ItemFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $form = new ItemForm(null, $options);
        $form->setEntityManager($services->get('Omeka\EntityManager'));
        return $form;
    }
}
