<?php
namespace Datascribe\PermissionsAssertion;

use Datascribe\Entity\DatascribeDataset;
use Datascribe\Entity\DatascribeItem;
use Datascribe\Entity\DatascribeProject;
use Datascribe\Entity\DatascribeRecord;
use Datascribe\Entity\DatascribeUser;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Assert that a DataScribe transcriber can do something.
 */
class TranscriberCanAssertion implements AssertionInterface
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
        if (DatascribeUser::ROLE_TRANSCRIBER !== $projectUser->getRole()) {
            // The user is not a transcriber for this project.
            return false;
        }
        // Handle item-specific permission checks.
        if ($resource instanceof DatascribeItem) {
            if ('datascribe_add_record' === $privilege) {
                return $this->canAddRecord($resource, $role);
            }
            if ('datascribe_mark_item_submitted' === $privilege) {
                return $this->canMarkItemSubmitted($resource, $role);
            }
            if ('datascribe_mark_item_not_submitted' === $privilege) {
                return $this->canMarkItemNotSubmitted($resource, $role);
            }
            if ('datascribe_unlock_item' === $privilege) {
                return $this->canUnlockItem($resource, $role);
            }
            if ('datascribe_lock_item_to_self' === $privilege) {
                return $this->canLockItemToSelf($resource);
            }
            if ('datascribe_edit_submit_action' === $privilege) {
                return (
                    $this->canMarkItemSubmitted($resource, $role)
                    || $this->canMarkItemNotSubmitted($resource, $role)
                );
            }
            if ('datascribe_edit_lock_action' === $privilege) {
                return (
                    $this->canUnlockItem($resource, $role)
                    || $this->canLockItemToSelf($resource)
                );
            }
        }
        // Handle record-specific permission checks.
        if ($resource instanceof DatascribeRecord) {
            if ('update' === $privilege) {
                return $this->canUpdateRecord($resource, $role);
            }
            if ('delete' === $privilege) {
                return $this->canDeleteRecord($resource, $role);
            }
        }
        return true;
    }

    public function canUpdateRecord($record, $user)
    {
        // - The item must be locked to the transcriber
        // - AND the item must not be approved
        $item = $record->getItem();
        return (
            $user === $item->getLockedBy()
            && true !== $item->getIsApproved()
        );
    }

    public function canDeleteRecord($record, $user)
    {
        // - The item must be locked to the transcriber
        // - AND the item must not be approved
        $item = $record->getItem();
        return (
            $user === $item->getLockedBy()
            && true !== $item->getIsApproved()
        );
    }

    public function canAddRecord($item, $user)
    {
        // - The item must be locked to the transcriber
        // - AND the item must not be approved
        return (
            $user === $item->getLockedBy()
            && true !== $item->getIsApproved()
        );
    }

    public function canMarkItemSubmitted($item, $user)
    {
        // - The item must be locked to the transcriber
        // - AND the item must not already be submitted for review
        // - AND the item must not be approved
        return (
            $user === $item->getLockedBy()
            && $item->getReviewed() >= $item->getSubmitted()
            && true !== $item->getIsApproved()
        );
    }

    public function canMarkItemNotSubmitted($item, $user)
    {
        // - The item must be locked to the transcriber
        // - AND the item must already be submitted for review
        // - AND the item must not be approved
        return (
            $user === $item->getLockedBy()
            && $item->getReviewed() < $item->getSubmitted()
            && true !== $item->getIsApproved()
        );
    }

    public function canUnlockItem($item, $user)
    {
        // - The item must be locked to the transcriber
        // - AND the item must not be approved
        return (
            $user === $item->getLockedBy()
            && true !== $item->getIsApproved()
        );
    }

    public function canLockItemToSelf($item)
    {
        // - The item must not already be locked
        // - AND the item must not be approved
        return (
            null === $item->getLocked()
            && true !== $item->getIsApproved()
        );
    }
}
