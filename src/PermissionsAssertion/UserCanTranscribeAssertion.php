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
 * Assert that an Omeka user can trnscribe a DataScribe item.
 */
class UserCanTranscribeAssertion implements AssertionInterface
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
        if (DatascribeUser::ROLE_TRANSCRIBER !== $projectUser->getRole()) {
            // The user is not a transcriber for this project.
            return false;
        }
        // Handle item-specific permission checks.
        if ($resource instanceof DatascribeItem) {
            if ('datascribe_mark_item_submitted' === $privilege) {
                // - The item must be locked to the user
                // - The item must not already be submitted for review
                // - The item must not be approved
                return (
                    $role === $resource->getLockedBy()
                    && $resource->getReviewed() >= $resource->getSubmitted()
                    && true !== $resource->getIsApproved()
                );
            }
            if ('datascribe_mark_item_not_submitted' === $privilege) {
                // - The item must be locked to the user
                // - The item must already be submitted for review
                return (
                    $role === $resource->getLockedBy()
                    && $resource->getReviewed() < $resource->getSubmitted()
                );
            }
        }
        return true;
    }
}
