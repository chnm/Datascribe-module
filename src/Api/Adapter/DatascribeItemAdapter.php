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
        if (isset($query['locked_status'])) {
            switch ($query['locked_status']) {
                case 'unlocked':
                    $qb->andWhere($qb->expr()->isNull('omeka_root.locked'));
                    break;
                case 'locked':
                    $qb->andWhere($qb->expr()->isNotNull('omeka_root.locked'));
                    break;
                default:
                    break;
            }
        }
        if (isset($query['review_status'])) {
            $this->buildReviewStatusQuery($qb, $query['review_status']);
        }
        if (isset($query['locked_by_id']) && is_numeric($query['locked_by_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $query['locked_by_id'])
            ));
        }
        if (isset($query['submitted_by_id']) && is_numeric($query['submitted_by_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.submittedBy',
                $this->createNamedParameter($qb, $query['submitted_by_id'])
            ));
        }
        if (isset($query['reviewed_by_id']) && is_numeric($query['reviewed_by_id'])) {
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.reviewedBy',
                $this->createNamedParameter($qb, $query['reviewed_by_id'])
            ));
        }

        // Set filters.
        $identity = $this->getServiceLocator()->get('Omeka\AuthenticationService')->getIdentity();
        if (isset($query['my_new'])) {
            $this->buildReviewStatusQuery($qb, 'new');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_in_progress'])) {
            $this->buildReviewStatusQuery($qb, 'in_progress');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_need_review'])) {
            $this->buildReviewStatusQuery($qb, 'need_review');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_not_approved'])) {
            $this->buildReviewStatusQuery($qb, 'not_approved');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_approved'])) {
            $this->buildReviewStatusQuery($qb, 'approved');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.lockedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_reviewed_and_need_review'])) {
            $this->buildReviewStatusQuery($qb, 'need_review');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.reviewedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_reviewed_and_not_approved'])) {
            $this->buildReviewStatusQuery($qb, 'not_approved');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.reviewedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['my_reviewed_and_approved'])) {
            $this->buildReviewStatusQuery($qb, 'approved');
            $qb->andWhere($qb->expr()->eq(
                'omeka_root.reviewedBy',
                $this->createNamedParameter($qb, $identity))
            );
        } elseif (isset($query['all_unlocked'])) {
            $qb->andWhere($qb->expr()->isNull('omeka_root.locked'));
        } elseif (isset($query['all_new'])) {
            $this->buildReviewStatusQuery($qb, 'new');
        } elseif (isset($query['all_in_progress'])) {
            $this->buildReviewStatusQuery($qb, 'in_progress');
        } elseif (isset($query['all_need_initial_review'])) {
            $this->buildReviewStatusQuery($qb, 'need_initial_review');
        } elseif (isset($query['all_need_review'])) {
            $this->buildReviewStatusQuery($qb, 'need_review');
        } elseif (isset($query['all_not_approved'])) {
            $this->buildReviewStatusQuery($qb, 'not_approved');
        } elseif (isset($query['all_approved'])) {
            $this->buildReviewStatusQuery($qb, 'approved');
        }
    }

    protected function buildReviewStatusQuery(QueryBuilder $qb, $reviewStatus)
    {
        switch ($reviewStatus) {
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
                $qb->addOrderBy("omeka_root.submitted", $query['sort_order']);
                break;
            case 'reviewed':
                $qb->addOrderBy("omeka_root.reviewed", $query['sort_order']);
                break;
            default:
                // Sort by priority by default.
                $qb->addOrderBy("omeka_root.prioritized", $query['sort_order']);
                break;
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
