<?php
namespace Datascribe\Api\Adapter;

use Datascribe\Entity\DatascribeItem;
use DateTime;
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
        if (isset($query['datascribe_dataset_id']) && is_numeric($query['datascribe_dataset_id'])) {
            $alias = $this->createAlias();
            $qb->innerJoin('omeka_root.dataset', $alias);
            $qb->andWhere($qb->expr()->eq(
                "$alias.id",
                $this->createNamedParameter($qb, $query['datascribe_dataset_id']))
            );
        }
        if (isset($query['item_id']) && is_numeric($query['item_id'])) {
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
        if (isset($query['item_set_id'])) {
            $itemSets = $query['item_set_id'];
            if (!is_array($itemSets)) {
                $itemSets = [$itemSets];
            }
            $itemSets = array_filter($itemSets, 'is_numeric');
            if ($itemSets) {
                $itemAlias = $this->createAlias();
                $qb->innerJoin('omeka_root.item', $itemAlias);
                $itemSetAlias = $this->createAlias();
                $qb->innerJoin(
                    sprintf('%s.itemSets', $itemAlias),
                    $itemSetAlias, 'WITH',
                    $qb->expr()->in("$itemSetAlias.id", $this->createNamedParameter($qb, $itemSets))
                );
            }
        }
        if (isset($query['status'])) {
            $this->buildStatusQuery($qb, $query['status']);
        }
        if (isset($query['locked_status'])) {
            if (is_numeric($query['locked_status'])) {
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.locked'));
                $qb->andWhere($qb->expr()->eq(
                    'omeka_root.lockedBy',
                    $this->createNamedParameter($qb, $query['locked_status'])
                ));
            } elseif ('not_locked' === $query['locked_status']) {
                $qb->andWhere($qb->expr()->isNull('omeka_root.locked'));
            } elseif ('locked' === $query['locked_status']) {
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.locked'));
            }
        }
        if (isset($query['submitted_status'])) {
            if (is_numeric($query['submitted_status'])) {
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.submitted'));
                $qb->andWhere($qb->expr()->eq(
                    'omeka_root.submittedBy',
                    $this->createNamedParameter($qb, $query['submitted_status'])
                ));
            } elseif ('not_submitted' === $query['submitted_status']) {
                $qb->andWhere($qb->expr()->isNull('omeka_root.submitted'));
            } elseif ('submitted' === $query['submitted_status']) {
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.submitted'));
            }
        }
        if (isset($query['reviewed_status'])) {
            if (is_numeric($query['reviewed_status'])) {
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.reviewed'));
                $qb->andWhere($qb->expr()->eq(
                    'omeka_root.reviewedBy',
                    $this->createNamedParameter($qb, $query['reviewed_status'])
                ));
            } elseif ('not_reviewed' === $query['reviewed_status']) {
                $qb->andWhere($qb->expr()->isNull('omeka_root.reviewed'));
            } elseif ('reviewed' === $query['reviewed_status']) {
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.reviewed'));
            }
        }

        // Set filters.
        $identity = $this->getServiceLocator()->get('Omeka\AuthenticationService')->getIdentity();
        if (isset($query['my_new'])) {
            $this->buildStatusQuery($qb, 'new');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_in_progress'])) {
            $this->buildStatusQuery($qb, 'in_progress');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_need_review'])) {
            $this->buildStatusQuery($qb, 'need_review');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_not_approved'])) {
            $this->buildStatusQuery($qb, 'not_approved');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_approved'])) {
            $this->buildStatusQuery($qb, 'approved');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_reviewed_and_need_review'])) {
            $this->buildStatusQuery($qb, 'need_review');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.reviewedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_reviewed_and_not_approved'])) {
            $this->buildStatusQuery($qb, 'not_approved');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.reviewedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_reviewed_and_approved'])) {
            $this->buildStatusQuery($qb, 'approved');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.reviewedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['all_unlocked_and_new'])) {
            $qb->andWhere($qb->expr()->isNull('omeka_root.locked'));
            $this->buildStatusQuery($qb, 'new');
        } elseif (isset($query['all_unlocked_and_in_progress'])) {
            $qb->andWhere($qb->expr()->isNull('omeka_root.locked'));
            $this->buildStatusQuery($qb, 'in_progress');
        } elseif (isset($query['all_need_initial_review'])) {
            $this->buildStatusQuery($qb, 'need_initial_review');
        } elseif (isset($query['all_need_rereview'])) {
            $this->buildStatusQuery($qb, 'need_rereview');
        } elseif (isset($query['all_need_review'])) {
            $this->buildStatusQuery($qb, 'need_review');
        } elseif (isset($query['all_not_approved'])) {
            $this->buildStatusQuery($qb, 'not_approved');
        } elseif (isset($query['all_approved'])) {
            $this->buildStatusQuery($qb, 'approved');
        }
        if (isset($query['has_invalid_values'])) {
            $aliasRecord = $this->createAlias();
            $aliasValue = $this->createAlias();
            $subQb = $this->getEntityManager()->createQueryBuilder()
                ->select($aliasRecord)
                ->from('Datascribe\Entity\DatascribeRecord', $aliasRecord)
                ->innerJoin("$aliasRecord.values", $aliasValue)
                ->andWhere("$aliasRecord.item = omeka_root.id")
                ->andWhere($qb->expr()->eq("$aliasValue.isInvalid", true));
            if (in_array($query['has_invalid_values'], [true, 1, '1'], true)) {
                $qb->andWhere($qb->expr()->exists($subQb->getDQL()));
            } elseif (in_array($query['has_invalid_values'], [false, 0, '0'], true)) {
                $qb->andWhere($qb->expr()->not($qb->expr()->exists($subQb->getDQL())));
            }
        }
        if (isset($query['all_prioritized'])) {
            $qb->andWhere($qb->expr()->isNotNull('omeka_root.prioritized'));
        }
    }

    protected function buildStatusQuery(QueryBuilder $qb, $status)
    {
        switch ($status) {
            case 'new':
                $qb->andWhere($qb->expr()->isNull('omeka_root.submitted'));
                $qb->andWhere($qb->expr()->isNull('omeka_root.reviewed'));
                $qb->andWhere($qb->expr()->isNull('omeka_root.isApproved'));
                $alias = $this->createAlias();
                $qb->leftJoin('omeka_root.records', $alias);
                $qb->andHaving($qb->expr()->eq($qb->expr()->count("$alias.id"), 0));
                break;
            case 'in_progress':
                $qb->andWhere($qb->expr()->isNull('omeka_root.submitted'));
                $qb->andWhere($qb->expr()->isNull('omeka_root.reviewed'));
                $qb->andWhere($qb->expr()->isNull('omeka_root.isApproved'));
                $alias = $this->createAlias();
                $qb->leftJoin('omeka_root.records', $alias);
                $qb->andHaving($qb->expr()->gt($qb->expr()->count("$alias.id"), 0));
                break;
            case 'need_initial_review':
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.submitted'));
                $qb->andWhere($qb->expr()->isNull('omeka_root.reviewed'));
                $qb->andWhere($qb->expr()->isNull('omeka_root.isApproved'));
                break;
            case 'need_rereview':
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.submitted'));
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.reviewed'));
                $qb->andWhere($qb->expr()->gt('omeka_root.submitted', 'omeka_root.reviewed'));
                $qb->andWhere($qb->expr()->eq('omeka_root.isApproved', $this->createNamedParameter($qb, false)));
                break;
            case 'need_review':
                $qb->andWhere($qb->expr()->isNotNull('omeka_root.submitted'));
                $qb->andWhere($qb->expr()->orX(
                    // need initial review
                    $qb->expr()->andX(
                        $qb->expr()->isNull('omeka_root.reviewed'),
                        $qb->expr()->isNull('omeka_root.isApproved')
                    ),
                    // need re-review
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('omeka_root.reviewed'),
                        $qb->expr()->gt('omeka_root.submitted', 'omeka_root.reviewed'),
                        $qb->expr()->eq('omeka_root.isApproved', $this->createNamedParameter($qb, false))
                    )
                ));
                break;
            case 'not_approved':
                $qb->andWhere($qb->expr()->eq('omeka_root.isApproved', $this->createNamedParameter($qb, false)));
                $qb->andWhere($qb->expr()->orX(
                    // not submitted and not approved
                    $qb->expr()->isNull('omeka_root.submitted'),
                    // submitted and not approved
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('omeka_root.submitted'),
                        $qb->expr()->isNotNull('omeka_root.reviewed'),
                        $qb->expr()->lt('omeka_root.submitted', 'omeka_root.reviewed')
                    )
                ));
                break;
            case 'approved':
                $qb->andWhere($qb->expr()->eq('omeka_root.isApproved', $this->createNamedParameter($qb, true)));
                break;
            default:
                break;
        }
    }

    public function sortQuery(QueryBuilder $qb, array $query)
    {
        switch ($query['sort_by']) {
            case 'title':
                $alias = $this->createAlias();
                $qb->innerJoin('omeka_root.item', $alias);
                $qb->addOrderBy("$alias.title", $query['sort_order']);
                break;
            case 'submitted':
                $qb->addOrderBy('omeka_root.submitted', $query['sort_order']);
                break;
            case 'reviewed':
                $qb->addOrderBy('omeka_root.reviewed', $query['sort_order']);
                break;
            case 'prioritized':
                $qb->addOrderBy('omeka_root.prioritized', $query['sort_order']);
                break;
        }
        if ('id' !== $query['sort_by']) {
            // Order by ascending ID if not sorting by ID.
            $qb->addOrderBy('omeka_root.id', 'asc');
        }
    }

    public function validateRequest(Request $request, ErrorStore $errorStore)
    {
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $services = $this->getServiceLocator();
        $currentUser = $services->get('Omeka\AuthenticationService')->getIdentity();
        $acl = $services->get('Omeka\Acl');

        // Handle a priority action.
        $priorityAction = $request->getValue('priority_action');
        if ('not_prioritized' === $priorityAction && $acl->userIsAllowed($entity, 'datascribe_mark_item_not_prioritized')) {
            $entity->setPrioritized(null);
            $entity->setPrioritizedBy(null);
        } elseif ('prioritized' === $priorityAction && $acl->userIsAllowed($entity, 'datascribe_mark_item_prioritized')) {
            $entity->setPrioritized(new DateTime('now'));
            $entity->setPrioritizedBy($currentUser);
        }

        // Handle a lock action.
        $lockAction = $request->getValue('lock_action');
        if ('unlock' === $lockAction && $acl->userIsAllowed($entity, 'datascribe_unlock_item')) {
            $entity->setLocked(null);
            $entity->setLockedBy(null);
        } elseif ('lock' === $lockAction && $acl->userIsAllowed($entity, 'datascribe_lock_item_to_self')) {
            $entity->setLocked(new DateTime('now'));
            $entity->setLockedBy($currentUser);
        } elseif (is_numeric($lockAction) && $acl->userIsAllowed($entity, 'datascribe_lock_item_to_other')) {
            $user = $this->getAdapter('users')->findEntity($lockAction);
            $entity->setLocked(new DateTime('now'));
            $entity->setLockedBy($user);
        }

        // Handle a submit action.
        $submitAction = $request->getValue('submit_action');
        if ('not_submitted' === $submitAction && $acl->userIsAllowed($entity, 'datascribe_mark_item_not_submitted')) {
            $entity->setSubmitted(null);
            $entity->setSubmittedBy(null);
        } elseif ('submitted' === $submitAction && $acl->userIsAllowed($entity, 'datascribe_mark_item_submitted')) {
            $entity->setSubmitted(new DateTime('now'));
            $entity->setSubmittedBy($currentUser);
        }

        // Handle a review action.
        $reviewAction = $request->getValue('review_action');
        if ('not_reviewed' === $reviewAction && $acl->userIsAllowed($entity, 'datascribe_mark_item_not_reviewed')) {
            $entity->setReviewed(null);
            $entity->setReviewedBy(null);
            $entity->setIsApproved(null);
        } elseif ('not_approved' === $reviewAction && $acl->userIsAllowed($entity, 'datascribe_mark_item_not_approved')) {
            $entity->setReviewed(new DateTime('now'));
            $entity->setReviewedBy($currentUser);
            $entity->setIsApproved(false);
        } elseif ('approved' === $reviewAction && $acl->userIsAllowed($entity, 'datascribe_mark_item_approved')) {
            $entity->setReviewed(new DateTime('now'));
            $entity->setReviewedBy($currentUser);
            $entity->setIsApproved(true);
        }

        // Handle transcriber notes.
        if ($this->shouldHydrate($request, 'o-module-datascribe:transcriber_notes') && $acl->userIsAllowed($entity, 'datascribe_edit_transcriber_notes')) {
            $entity->setTranscriberNotes($request->getValue('o-module-datascribe:transcriber_notes'));
        }

        // Handle reviewer notes.
        if ($this->shouldHydrate($request, 'o-module-datascribe:reviewer_notes') && $acl->userIsAllowed($entity, 'datascribe_edit_reviewer_notes')) {
            $entity->setReviewerNotes($request->getValue('o-module-datascribe:reviewer_notes'));
        }
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

    public function preprocessBatchUpdate(array $data, Request $request)
    {
        $data = parent::preprocessBatchUpdate($data, $request);
        $rawData = $request->getContent();
        $data['priority_action'] = $rawData['priority_action'] ?? null;
        $data['lock_action'] = $rawData['lock_action'] ?? null;
        $data['submit_action'] = $rawData['submit_action'] ?? null;
        $data['review_action'] = $rawData['review_action'] ?? null;
        return $data;
    }

    public function getInvalidValueCount(DatascribeItem $item)
    {
        $services = $this->getServiceLocator();
        $em = $services->get('Omeka\EntityManager');
        $dql = '
            SELECT COUNT(v.id)
            FROM Datascribe\Entity\DatascribeRecord r
            JOIN r.values v
            WHERE r.item = :itemId
            AND v.isInvalid = true';
        $query = $em->createQuery($dql);
        $query->setParameter('itemId', $item->getId());
        return $query->getSingleScalarResult();
    }
}
