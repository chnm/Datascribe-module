<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeProjectRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-datascribe:Project';
    }

    public function getJsonLd()
    {
        $owner = $this->owner();
        $createdBy = $this->createdBy();
        $modifiedBy = $this->modifiedBy();
        $modified = $this->modified();
        return [
            'o-module-datascribe:name' => $this->name(),
            'o-module-datascribe:description' => $this->description(),
            'o:is_public' => $this->isPublic(),
            'o:created' => $this->getDateTime($this->created()),
            'o:modified' => $modified ? $this->getDateTime($modified) : null,
            'o:owner' => $owner ? $owner->getReference() : null,
            'o-module-datascribe:created_by' => $createdBy ? $createdBy->getReference() : null,
            'o-module-datascribe:modified_by' => $modifiedBy ? $modifiedBy->getReference() : null,
            'o-module-datascribe:user' => $this->users(),
        ];
    }

    public function adminUrl($action = null, $canonical = false)
    {
        $url = $this->getViewHelper('Url');
        return $url(
            'admin/datascribe-project-id',
            [
                'action' => $action,
                'project-id' => $this->resource->getId(),
            ],
            ['force_canonical' => $canonical]
        );
    }

    public function name()
    {
        return $this->resource->getName();
    }

    public function description()
    {
        return $this->resource->getDescription();
    }

    public function isPublic()
    {
        return $this->resource->getIsPublic();
    }

    public function created()
    {
        return $this->resource->getCreated();
    }

    public function modified()
    {
        return $this->resource->getModified();
    }

    public function owner()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getOwner());
    }

    public function createdBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getCreatedBy());
    }

    public function modifiedBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getModifiedBy());
    }

    public function users()
    {
        $users = [];
        foreach ($this->resource->getUsers() as $user) {
            $users[] = new DatascribeUserRepresentation($user, $this->getServiceLocator());
        }
        return $users;
    }
}
