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
            'The DatascribeItemAdapter adapter does not implement the create operation.' // @translate
        );
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
        if (isset($query['datascribe_dataset_id'])) {
            $alias = $this->createAlias();
            $qb->innerJoin('omeka_root.dataset', $alias);
            $qb->andWhere($qb->expr()->eq(
                "$alias.id",
                $this->createNamedParameter($qb, $query['datascribe_dataset_id']))
            );
        }
        if (isset($query['item_id'])) {
            $alias = $this->createAlias();
            $qb->innerJoin('omeka_root.item', $alias);
            $qb->andWhere($qb->expr()->eq(
                "$alias.id",
                $this->createNamedParameter($qb, $query['item_id']))
            );
        }
        if (isset($query['resource_class_id'])) {
            $classes = $query['resource_class_id'];
            if (!is_array($classes)) {
                $classes = [$classes];
            }
            $classes = array_filter($classes, 'is_numeric');
            if ($classes) {
                $alias = $this->createAlias();
                $qb->innerJoin('omeka_root.item', $alias);
                $qb->andWhere($qb->expr()->in(
                    "$alias.resourceClass",
                    $this->createNamedParameter($qb, $classes)
                ));
            }
        }
        if (isset($query['resource_template_id'])) {
            $templates = $query['resource_template_id'];
            if (!is_array($templates)) {
                $templates = [$templates];
            }
            $templates = array_filter($templates, 'is_numeric');
            if ($templates) {
                $alias = $this->createAlias();
                $qb->innerJoin('omeka_root.item', $alias);
                $qb->andWhere($qb->expr()->in(
                    "$alias.resourceTemplate",
                    $this->createNamedParameter($qb, $templates)
                ));
            }
        }
        if (isset($query['search'])) {
            // Filter by search query. Equivalent to property=null, type=in.
            $value = $query['search'];
            $itemAlias = $this->createAlias();
            $valueAlias = $this->createAlias();
            $param = $this->createNamedParameter($qb, "%$value%");
            $qb->leftJoin('omeka_root.item', $itemAlias)
                ->leftJoin("$itemAlias.values", $valueAlias)
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like("$valueAlias.value", $param),
                    $qb->expr()->like("$valueAlias.uri", $param)
                ));
        }
    }

    public function validateRequest(Request $request, ErrorStore $errorStore)
    {
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        if (null === $entity->getDataset()) {
            $errorStore->addError('o-module-dataset:dataset', 'A DataScribe dataset must not be null'); // @translate
        }
        if (null === $entity->getItem()) {
            $errorStore->addError('o:item', 'An item must not be null'); // @translate
        }
    }
}
