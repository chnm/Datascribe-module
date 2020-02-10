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
        return true;
    }
}
