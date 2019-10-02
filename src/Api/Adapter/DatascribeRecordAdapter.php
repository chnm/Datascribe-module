<?php
namespace Datascribe\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class DatascribeRecordAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [];

    public function getResourceName()
    {
        return 'datascribe_records';
    }

    public function getRepresentationClass()
    {
        return 'Datascribe\Api\Representation\DatascribeRecordRepresentation';
    }

    public function getEntityClass()
    {
        return 'Datascribe\Entity\DatascribeRecord';
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
