<?php
namespace Datascribe\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Exception;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class DatascribeItemAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [];

    public function getResourceName()
    {
        return 'datascribe_items';
    }

    public function getRepresentationClass()
    {
        return 'Datascribe\Api\Representation\DatascribeItemRepresentation';
    }

    public function getEntityClass()
    {
        return 'Datascribe\Entity\DatascribeItem';
    }

    public function create(Request $request)
    {
        // DataScribe items are created only when a dataset is synced.
        throw new Exception\OperationNotImplementedException(
            'The Datascribe\Api\Adapter\DatascribeItemAdapter adapter does not implement the create operation.' // @translate
        );
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
    }
    public function validateRequest(Request $request, ErrorStore $errorStore)
    {
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
    }
}
