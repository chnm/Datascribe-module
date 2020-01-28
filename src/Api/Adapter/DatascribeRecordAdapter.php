<?php
namespace Datascribe\Api\Adapter;

use Datascribe\Entity\DatascribeValue;
use Doctrine\Common\Collections\ArrayCollection;
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
            if (!isset($data['o-module-datascribe:item'])
                || !isset($data['o-module-datascribe:item']['o:id'])
                || !is_numeric($data['o-module-datascribe:item']['o:id'])
            ) {
                $errorStore->addError('o-module-datascribe:item', 'Invalid item format passed in request.'); // @translate
            }
        }
        if (isset($data['o:owner']) && !isset($data['o:owner']['o:id'])) {
            $errorStore->addError('o:owner', 'Invalid owner format passed in request.'); // @translate
        }
        if (isset($data['o-module-datascribe:value'])) {
            if (!is_array($data['o-module-datascribe:value'])) {
                $errorStore->addError('o-module-datascribe:value', 'Invalid values format passed in request.'); // @translate
            } else {
                foreach ($data['o-module-datascribe:value'] as $fieldId => $valueData) {
                    if (!isset($valueData['is_missing'])) {
                        $errorStore->addError('is_missing', sprintf('Invalid value format passed in request. Missing "is_missing" for field #%s.', $fieldId));
                    }
                    if (!isset($valueData['is_illegible'])) {
                        $errorStore->addError('is_illegible', sprintf('Invalid value format passed in request. Missing "is_illegible" for field #%s.', $fieldId));
                    }
                    if (!isset($valueData['data'])) {
                        $errorStore->addError('data', sprintf('Invalid value format passed in request. Missing "data" for field #%s.', $fieldId));
                    } elseif (!is_array($valueData['data'])) {
                        $errorStore->addError('data', sprintf('Invalid value format passed in request. Invalid "data" format for field #%s.', $fieldId));
                    }
                }
            }
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $services = $this->getServiceLocator();
        $em = $services->get('Omeka\EntityManager');
        $dataTypes = $services->get('Datascribe\DataTypeManager');
        if (Request::CREATE === $request->getOperation()) {
            $itemData = $request->getValue('o-module-datascribe:item');
            $item = $this->getAdapter('datascribe_items')->findEntity($itemData['o:id']);
            $entity->setItem($item);
        }
        $this->hydrateOwner($request, $entity);
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
            $values = $entity->getValues();
            $valuesToRetain = new ArrayCollection;
            foreach ($request->getValue('o-module-datascribe:value') as $fieldId => $valueData) {
                $field = $em->getReference('Datascribe\Entity\DatascribeField', $fieldId);
                if ($values->containsKey($fieldId)) {
                    // This is an existing value.
                    $value = $values->get($field->getId());
                } else {
                    // This is a new value.
                    $value = new DatascribeValue;
                    $value->setField($field);
                    $value->setRecord($entity);
                    $values->add($value);
                }
                $value->setIsMissing($valueData['is_missing']);
                $value->setIsIllegible($valueData['is_illegible']);
                $dataType = $dataTypes->get($field->getDataType());
                $value->setData($dataType->getValueData($valueData['data']));
                $valuesToRetain->add($value);
            }
            // Remove values not passed in the request.
            foreach ($values as $value) {
                if (!$valuesToRetain->contains($value)) {
                    $values->removeElement($value);
                }
            }
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        $services = $this->getServiceLocator();
        $dataTypes = $services->get('Datascribe\DataTypeManager');

        $item = $entity->getItem();
        if (null === $item) {
            $errorStore->addError('o-module-datascribe:item', 'Missing item.'); // @translate
        }
        $fields = $item->getDataset()->getFields();
        foreach ($entity->getValues() as $value) {
            $field = $value->getField();
            // Validate the field. It must be assigned to the item's dataset.
            if (!$fields->containsKey($field->getId())) {
                $errorStore->addError('data', 'Invalid field. Field not in dataset.'); // @translate
            }

            // Validate the value data.
            $dataType = $dataTypes->get($field->getDataType());
            if (!$dataType->valueDataIsValid($field->getData(), $value->getData())) {
                $errorStore->addError('data', sprintf('Invalid value data for field "%s".', $field->getLabel())); // @translate
            }
        }
    }
}
