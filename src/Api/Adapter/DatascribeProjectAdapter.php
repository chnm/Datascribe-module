<?php
namespace Datascribe\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class DatascribeProjectAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [];

    public function getResourceName()
    {
        return 'datascribe_projects';
    }

    public function getRepresentationClass()
    {
        return 'Datascribe\Api\Representation\DatascribeProjectRepresentation';
    }

    public function getEntityClass()
    {
        return 'Datascribe\Entity\DatascribeProject';
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
