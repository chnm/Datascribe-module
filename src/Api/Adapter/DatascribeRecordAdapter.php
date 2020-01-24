<?php
namespace Datascribe\Api\Adapter;

use Datascribe\Entity\DatascribeValue;
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
        if (isset($query['datascribe_item_id']) && is_numeric($query['datascribe_item_id'])) {
            $alias = $this->createAlias();
            $qb->innerJoin('omeka_root.item', $alias);
            $qb->andWhere($qb->expr()->eq(
                "$alias.id",
                $this->createNamedParameter($qb, $query['datascribe_item_id']))
            );
        }
    }

    public function validateRequest(Request $request, ErrorStore $errorStore)
    {
        $data = $request->getContent();
        if (Request::CREATE === $request->getOperation()) {
            if (!isset($data['o-module-datascribe:item'])) {
                $errorStore->addError('o-module-datascribe:item', 'A record must have an item'); // @translate
            } elseif (!isset($data['o-module-datascribe:item']['o:id'])) {
                $errorStore->addError('o-module-datascribe:item', 'An item must have an ID'); // @translate
            }
        }
        if (isset($data['o:owner']) && !isset($data['o:owner']['o:id'])) {
            $errorStore->addError('o:owner', 'An owner must have an ID'); // @translate
        }
        if (isset($data['o-module-datascribe:value'])) {
            if (is_array($data['o-module-datascribe:value'])) {
                foreach ($data['o-module-datascribe:value'] as $fieldId => $valueData) {
                    // @todo: Validate the structure for each value here ("is_missing", "is_illegible", and "data" must exist)
                }
            } else {
                $errorStore->addError('o-module-datascribe:value', 'Record values must be an array'); // @translate
            }
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $this->hydrateOwner($request, $entity);
        if (Request::CREATE === $request->getOperation()) {
            $itemData = $request->getValue('o-module-datascribe:item');
            $item = $this->getAdapter('datascribe_items')->findEntity($itemData['o:id']);
            $entity->setItem($item);
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:transcriber_notes')) {
            $entity->setTranscriberNotes($request->getValue('o-module-datascribe:transcriber_notes'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:reviewer_notes')) {
            $entity->setReviewerNotes($request->getValue('o-module-datascribe:reviewer_notes'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:needs_review')) {
            $entity->setNeedsReview($request->getValue('o-module-datascribe:needs_review'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:needs_work')) {
            $entity->setNeedsWork($request->getValue('o-module-datascribe:needs_work'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:value')) {
            $em = $this->getServiceLocator()->get('Omeka\EntityManager');
            $dataTypes = $this->getServiceLocator()->get('Datascribe\DataTypeManager');
            $values = $entity->getValues();
            foreach ($request->getValue('o-module-datascribe:value') as $fieldId => $valueData) {
                if ($values->containsKey($fieldId)) {
                    // This is an existing value.
                    $value = $values->get($fieldId);
                } else {
                    // This is a new value.
                    $value = new DatascribeValue;
                    $field = $em->getReference('Datascribe\Entity\DatascribeField', $fieldId);
                    $value->setField($field);
                    $value->setRecord($entity);
                    $values->add($value);
                }
                $value->setIsMissing($valueData['is_missing']);
                $value->setIsIllegible($valueData['is_illegible']);
                $dataType = $dataTypes->get($field->getDataType());
                $value->setData($dataType->getValueData($valueData['data']));
            }
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        if (null === $entity->getItem()) {
            $errorStore->addError('o-module-datascribe:item', 'An item must not be null'); // @translate
        }
        // @todo: Verify that all fields in "o-module-datascribe:value" exist and are assigned to this dataset (via the item).
    }
}
