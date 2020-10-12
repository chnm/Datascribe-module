<?php
namespace Datascribe\PermissionsAssertion;

use Datascribe\Entity\DatascribeDataset;
use Datascribe\Entity\DatascribeItem;
use Datascribe\Entity\DatascribeProject;
use Datascribe\Entity\DatascribeRecord;
use Datascribe\Entity\DatascribeUser;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Assert that a DataScribe reviewer can do something.
 */
class ReviewerCanAssertion implements AssertionInterface
{
    public function assert(Acl $acl, RoleInterface $role = null,
        ResourceInterface $resource = null, $privilege = null
    ) {
        if (!$role) {
            // The user is not authenticated.
            return false;
        }
        if ($resource instanceof DatascribeProject) {
            $project = $resource;
        } elseif ($resource instanceof DatascribeDataset) {
            $project = $resource->getProject();
        } elseif ($resource instanceof DatascribeItem) {
            $project = $resource->getDataset()->getProject();
        } elseif ($resource instanceof DatascribeRecord) {
            $project = $resource->getItem()->getDataset()->getProject();
        } else {
            // The resource is not valid.
            return false;
        }
        // The users collection is indexed by user_id.
        $projectUser = $project->getUsers()->get($role->getId());
        if (!$projectUser) {
            // The user is not assigned to this project.
            return false;
        }
        if (DatascribeUser::ROLE_REVIEWER !== $projectUser->getRole()) {
            // The user is not a reviewer for this project.
            return false;
        }
        // Handle item-specific permission checks.
        if ($resource instanceof DatascribeItem) {
            if ('datascribe_mark_item_submitted' === $privilege) {
                return $this->canMarkItemSubmitted($resource);
            }
            if ('datascribe_mark_item_not_submitted' === $privilege) {
                return $this->canMarkItemNotSubmitted($resource);
            }
            if ('datascribe_mark_item_not_reviewed' === $privilege) {
                return $this->canMarkItemNotReviewed($resource);
            }
            if ('datascribe_mark_item_not_approved' === $privilege) {
                return $this->canMarkItemNotApproved($resource);
            }
            if ('datascribe_mark_item_approved' === $privilege) {
                return $this->canMarkItemApproved($resource);
            }
            if ('datascribe_unlock_item' === $privilege) {
                return $this->canUnlockItem($resource);
            }
            if ('datascribe_lock_item_to_self' === $privilege) {
                return $this->canLockItemToSelf($resource, $role);
            }
            if ('datascribe_mark_item_prioritized' === $privilege) {
                return $this->canMarkItemPrioritized($resource);
            }
            if ('datascribe_mark_item_not_prioritized' === $privilege) {
                return $this->canMarkItemNotPrioritized($resource);
            }
            if ('datascribe_edit_submit_action' === $privilege) {
                return (
                    $this->canMarkItemSubmitted($resource)
                    || $this->canMarkItemNotSubmitted($resource)
                );
            }
            if ('datascribe_edit_review_action' === $privilege) {
                return (
                    $this->canMarkItemNotReviewed($resource)
                    || $this->canMarkItemNotApproved($resource)
                    || $this->canMarkItemApproved($resource)
                );
            }
            if ('datascribe_edit_lock_action' === $privilege) {
                return (
                    $this->canUnlockItem($resource)
                    || $this->canLockItemToSelf($resource, $role)
                );
            }
            if ('datascribe_edit_priority_action' === $privilege) {
                return (
                    $this->canMarkItemPrioritized($resource)
                    || $this->canMarkItemNotPrioritized($resource)
                );
            }
        }
        return true;
    }

    public function canMarkItemSubmitted($item)
    {
        // - The item must not already be submitted for review
        // - AND the item must not be approved
        return (
            $item->getReviewed() >= $item->getSubmitted()
            && true !== $item->getIsApproved()
        );
    }

    public function canMarkItemNotSubmitted($item)
    {
        // - The item must already be submitted for review
        return ($item->getReviewed() < $item->getSubmitted());
    }

    public function canMarkItemNotReviewed($item)
    {
        // - The item must be reviewed
        return $item->getReviewed();
    }

    public function canMarkItemNotApproved($item)
    {
        // - The item must not already be not approved
        return (false !== $item->getIsApproved());
    }

    public function canMarkItemApproved($item)
    {
        // - The item must not be approved.
        return (true !== $item->getIsApproved());
    }

    public function canUnlockItem($item)
    {
        // - The item must already be locked
        return $item->getLocked();
    }

    public function canLockItemToSelf($item, $user)
    {
        // - The item must not already be locked by self
        return ($user !== $item->getLockedBy());
    }

    public function canMarkItemPrioritized($item)
    {
        // - The item must not already be marked as prioritized
        return (null === $item->getPrioritized());
    }

    public function canMarkItemNotPrioritized($item)
    {
        // - The item must already be marked as prioritized
        return $item->getPrioritized();
    }
}
