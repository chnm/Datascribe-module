<?php
namespace Datascribe\PermissionsAssertion;

use Datascribe\Entity\DatascribeDataset;
use Datascribe\Entity\DatascribeItem;
use Datascribe\Entity\DatascribeProject;
use Datascribe\Entity\DatascribeUser;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Assert that an Omeka user can review a DataScribe item.
 */
class UserCanReviewAssertion implements AssertionInterface
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
                // - The item must not already be submitted for review
                // - AND the item must not be approved
                return (
                    $resource->getReviewed() >= $resource->getSubmitted()
                    && true !== $resource->getIsApproved()
                );
            }
            if ('datascribe_mark_item_not_submitted' === $privilege) {
                // - The item must already be submitted for review
                return ($resource->getReviewed() < $resource->getSubmitted());
            }
            if ('datascribe_mark_item_not_reviewed' === $privilege) {
                // - The item must be reviewed
                return $resource->getReviewed();
            }
            if ('datascribe_mark_item_not_approved' === $privilege) {
                // - The item must be submitted
                // - AND the item must be approved
                return (
                    $resource->getSubmitted()
                    && true === $resource->getIsApproved()
                );
            }
            if ('datascribe_mark_item_approved' === $privilege) {
                // - The item must not be approved.
                return (true !== $resource->getIsApproved());
            }
            if ('datascribe_unlock_item' === $privilege) {
                // - The item must already be locked
                return $resource->getLockedBy();
            }
            if ('datascribe_lock_item_to_self' === $privilege) {
                // - The item must not already be locked by self
                return $role !== $resource->getLockedBy();
            }
            if ('datascribe_mark_item_prioritized' === $privilege) {
                // - The item must not already be marked as prioritized
                return null === $resource->getPrioritizedBy();
            }
            if ('datascribe_mark_item_not_prioritized' === $privilege) {
                // - The item must already be marked as prioritized
                return $resource->getPrioritizedBy();
            }
        }
        return true;
    }
}
