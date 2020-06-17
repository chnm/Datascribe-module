<?php
namespace Datascribe\Api\Adapter;

use Datascribe\DatascribeDataType\Unknown;
use Datascribe\Entity\DatascribeRecord;
use Datascribe\Entity\DatascribeValue;
use DateTime;
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

    public function sortQuery(QueryBuilder $qb, array $query)
    {
        if (isset($query['sort_by'])) {
            if (is_numeric($query['sort_by'])) {
                // Sort by the values of a field.
                $alias = $this->createAlias();
                $qb->leftJoin(
                    "omeka_root.values", $alias,
                    'WITH', $qb->expr()->eq("$alias.field", $query['sort_by'])
                );
                $qb->addOrderBy(
                    "GROUP_CONCAT($alias.text ORDER BY $alias.id)",
                    $query['sort_order']
                );
            } elseif ('id' === $query['sort_by']) {
                $qb->addOrderBy('omeka_root.id', $query['sort_order']);
            } elseif (in_array($query['sort_by'], ['item_id', 'position'])) {
                $qb->addOrderBy('omeka_root.item', $query['sort_order']);
                $qb->addOrderBy('omeka_root.position', $query['sort_order']);
            }
        }
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
        if (isset($query['datascribe_dataset_id']) && is_numeric($query['datascribe_dataset_id'])) {
            $itemAlias = $this->createAlias();
            $qb->innerJoin('omeka_root.item', $itemAlias);
            $datasetAlias = $this->createAlias();
            $qb->innerJoin("$itemAlias.dataset", $datasetAlias);
            $qb->andWhere($qb->expr()->eq(
                "$datasetAlias.id",
                $this->createNamedParameter($qb, $query['datascribe_dataset_id']))
            );
        }
        if (isset($query['datascribe_item_id']) && is_numeric($query['datascribe_item_id'])) {
            $alias = $this->createAlias();
            $qb->innerJoin('omeka_root.item', $alias);
            $qb->andWhere($qb->expr()->eq(
                "$alias.id",
                $this->createNamedParameter($qb, $query['datascribe_item_id']))
            );
        }
        if (isset($query['needs_review'])) {
            if (in_array($query['needs_review'], [true, 1, '1'], true)) {
                $qb->andWhere($qb->expr()->eq('omeka_root.needsReview', 1));
            } elseif (in_array($query['needs_review'], [false, 0, '0'], true)) {
                $qb->andWhere($qb->expr()->eq('omeka_root.needsReview', 0));
            }
        }
        if (isset($query['needs_work'])) {
            if (in_array($query['needs_work'], [true, 1, '1'], true)) {
                $qb->andWhere($qb->expr()->eq('omeka_root.needsWork', 1));
            } elseif (in_array($query['needs_work'], [false, 0, '0'], true)) {
                $qb->andWhere($qb->expr()->eq('omeka_root.needsWork', 0));
            }
        }
        if (isset($query['created_by']) && is_numeric($query['created_by'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.createdBy',
                $this->createNamedParameter($qb, $query['created_by'])
            ));
        }
        if (isset($query['modified_by']) && is_numeric($query['modified_by'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.modifiedBy',
                $this->createNamedParameter($qb, $query['modified_by'])
            ));
        }
        if (isset($query['has_invalid_values'])) {
            $alias = $this->createAlias();
            $subQb = $this->getEntityManager()->createQueryBuilder()
                ->select($alias)
                ->from('Datascribe\Entity\DatascribeValue', $alias)
                ->andWhere("$alias.record = omeka_root.id")
                ->andWhere($qb->expr()->eq("$alias.isInvalid", true));
            if (in_array($query['has_invalid_values'], [true, 1, '1'], true)) {
                $qb->andWhere($qb->expr()->exists($subQb->getDQL()));
            } elseif (in_array($query['has_invalid_values'], [false, 0, '0'], true)) {
                $qb->andWhere($qb->expr()->not($qb->expr()->exists($subQb->getDQL())));
            }
        }
        if (isset($query['before_position']) && is_numeric($query['before_position'])) {
            $qb->andWhere($qb->expr()->lt('omeka_root.position', $query['before_position']));
            // Setting ORDER BY DESC here so a LIMIT won't cut off expected
            // rows. It's the consumer's responsibility to reverse the result
            // set if ORDER BY ASC is needed.
            $qb->orderBy('omeka_root.position', 'desc');
        } elseif (isset($query['after_position']) && is_numeric($query['after_position'])) {
            $qb->andWhere($qb->expr()->gt('omeka_root.position', $query['after_position']));
            $qb->orderBy('omeka_root.position', 'asc');
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
                    if (!$request->getOption('isPartial', false) && !isset($valueData['is_missing'])) {
                        $errorStore->addError('is_missing', sprintf('Invalid value format passed in request. Missing "is_missing" for field #%s.', $fieldId));
                    }
                    if (!$request->getOption('isPartial', false) && !isset($valueData['is_illegible'])) {
                        $errorStore->addError('is_illegible', sprintf('Invalid value format passed in request. Missing "is_illegible" for field #%s.', $fieldId));
                    }
                    if (!$request->getOption('isPartial', false) && !isset($valueData['data'])) {
                        $errorStore->addError('data', sprintf('Invalid value format passed in request. Missing "data" for field #%s.', $fieldId));
                    }
                    if (isset($valueData['data']) && !is_array($valueData['data'])) {
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
        $user = $services->get('Omeka\AuthenticationService')->getIdentity();
        $acl = $services->get('Omeka\Acl');

        $this->hydrateOwner($request, $entity);
        if (Request::CREATE === $request->getOperation()) {
            $itemData = $request->getValue('o-module-datascribe:item');
            $item = $this->getAdapter('datascribe_items')->findEntity($itemData['o:id']);
            $entity->setItem($item);
            $entity->setCreatedBy($user);
            $entity->setNeedsReview(false);
            $entity->setNeedsWork(false);
        } else {
            $entity->setModifiedBy($user);
            $entity->setModified(new DateTime('now'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:transcriber_notes') && $acl->userIsAllowed($entity->getItem(), 'datascribe_edit_transcriber_notes')) {
            $entity->setTranscriberNotes($request->getValue('o-module-datascribe:transcriber_notes'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:reviewer_notes') && $acl->userIsAllowed($entity->getItem(), 'datascribe_edit_reviewer_notes')) {
            $entity->setReviewerNotes($request->getValue('o-module-datascribe:reviewer_notes'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:needs_review') && $acl->userIsAllowed($entity->getItem(), 'datascribe_flag_record_needs_review')) {
            $entity->setNeedsReview($request->getValue('o-module-datascribe:needs_review'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:needs_work') && $acl->userIsAllowed($entity->getItem(), 'datascribe_flag_record_needs_work')) {
            $entity->setNeedsWork($request->getValue('o-module-datascribe:needs_work'));
        }
        $positionDirection = $request->getValue('position_change_direction');
        $positionRecordId = $request->getValue('position_change_record_id');
        if (isset($positionDirection, $positionRecordId)
            && in_array($positionDirection, ['before', 'after'])
            && is_numeric($positionRecordId)
            && $acl->userIsAllowed($entity->getItem(), 'datascribe_change_record_position')
        ) {
            $entity->setPositionChange($positionDirection, $positionRecordId);
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:value')) {
            $values = $entity->getValues();
            $valuesToRetain = new ArrayCollection;
            foreach ($request->getValue('o-module-datascribe:value') as $fieldId => $valueData) {
                $field = $em->getReference('Datascribe\Entity\DatascribeField', $fieldId);
                $dataType = $dataTypes->get($field->getDataType());
                // Get an existing value or create a new value.
                if ($values->containsKey($fieldId)) {
                    // This is an existing value.
                    $value = $values->get($field->getId());
                } else {
                    // This is a new value.
                    $value = new DatascribeValue;
                    $value->setField($field);
                    $value->setRecord($entity);
                    $values->set($fieldId, $value);
                }
                // Set is_missing.
                $isMissing = $value->getIsMissing();
                if (isset($valueData['is_missing'])) {
                    $isMissing = (bool) $valueData['is_missing'];
                    $value->setIsMissing($isMissing);
                }
                // Set is_illegible.
                $isIllegible = $value->getIsIllegible();
                if (isset($valueData['is_illegible'])) {
                    $isIllegible = (bool) $valueData['is_illegible'];
                    $value->setIsIllegible($isIllegible);
                }
                // Set value text.
                $valueText = $value->getText();
                if (isset($valueData['set_null']) && $valueData['set_null']) {
                    $valueText = null;
                } elseif (isset($valueData['data'])) {
                    $valueText = $dataType->getValueTextFromUserData($valueData['data']);
                }
                if (!($dataType instanceof Unknown)) {
                    // Set value text only when the data type is known.
                    $value->setText($valueText);
                }
                // Set is_invalid.
                $value->setIsInvalid(false);
                if ((null === $valueText) && $field->getIsRequired() && !$isMissing && !$isIllegible) {
                    // Null text is invalid if the field is required and the
                    // value is not missing and not illegible.
                    $value->setIsInvalid(true);
                }
                $valuesToRetain->add($value);
            }
            // Remove values not passed in the request.
            if (!$request->getOption('isPartial', false)) {
                foreach ($values as $value) {
                    if (!$valuesToRetain->contains($value)) {
                        $values->removeElement($value);
                    }
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

            // Validate the value text. Null values should never raise an error.
            if (null !== $value->getText()) {
                $dataType = $dataTypes->get($field->getDataType());
                if (!$dataType->valueTextIsValid($field->getData(), $value->getText())) {
                    $errorStore->addError('data', sprintf('Invalid value text for field "%s".', $field->getName())); // @translate
                }
            }
        }
    }

    public function getInvalidValueCount(DatascribeRecord $record)
    {
        $services = $this->getServiceLocator();
        $em = $services->get('Omeka\EntityManager');
        $dql = '
            SELECT COUNT(v.id)
            FROM Datascribe\Entity\DatascribeValue v
            WHERE v.record = :recordId
            AND v.isInvalid = true';
        $query = $em->createQuery($dql);
        $query->setParameter('recordId', $record->getId());
        return $query->getSingleScalarResult();
    }

    public function preprocessBatchUpdate(array $data, Request $request)
    {
        $data = parent::preprocessBatchUpdate($data, $request);
        $rawData = $request->getContent();
        if (in_array($rawData['needs_review_action'], [true, 1, '1'], true)) {
            $data['o-module-datascribe:needs_review'] = 1;
        } elseif (in_array($rawData['needs_review_action'], [false, 0, '0'], true)) {
            $data['o-module-datascribe:needs_review'] = 0;
        }
        if (in_array($rawData['needs_work_action'], [true, 1, '1'], true)) {
            $data['o-module-datascribe:needs_work'] = 1;
        } elseif (in_array($rawData['needs_work_action'], [false, 0, '0'], true)) {
            $data['o-module-datascribe:needs_work'] = 0;
        }
        foreach ($rawData['values'] as $fieldId => $valueData) {
            if (in_array($valueData['is_missing_action'], [true, 1, '1'], true)) {
                $data['o-module-datascribe:value'][$fieldId]['is_missing'] = 1;
            } elseif (in_array($valueData['is_missing_action'], [false, 0, '0'], true)) {
                $data['o-module-datascribe:value'][$fieldId]['is_missing'] = 0;
            }
            if (in_array($valueData['is_illegible_action'], [true, 1, '1'], true)) {
                $data['o-module-datascribe:value'][$fieldId]['is_illegible'] = 1;
            } elseif (in_array($valueData['is_illegible_action'], [false, 0, '0'], true)) {
                $data['o-module-datascribe:value'][$fieldId]['is_illegible'] = 0;
            }
            if (in_array($valueData['edit_values'], [true, 1, '1'], true)) {
                $data['o-module-datascribe:value'][$fieldId]['data'] = $valueData['data'];
            }
        }
        return $data;
    }
}
