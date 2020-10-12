<?php
namespace Datascribe\PermissionsAssertion;

use Datascribe\Entity\DatascribeItem;
use Datascribe\Entity\DatascribeRecord;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class IsDatascribeUserAssertion implements AssertionInterface
{
    public function assert(Acl $acl, RoleInterface $role = null,
        ResourceInterface $resource = null, $privilege = null
    ) {
        if ($resource instanceof DatascribeItem) {
            $project = $resource->getDataset()->getProject();
        } elseif ($resource instanceof DatascribeRecord) {
            $project = $resource->getItem()->getDataset()->getProject();
        } else {
            return false;
        }
        $projectUser = $project->getUsers()->get($role->getId());
        return (bool) $projectUser;
    }
}
