<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeDatasetRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-datascribe:Dataset';
    }

    public function getJsonLd()
    {
        $owner = $this->owner();
        $synced = $this->synced();
        $syncedBy = $this->syncedBy();
        $itemSet = $this->itemSet();
        return [
            'o-module-datascribe:name' => $this->name(),
            'o-module-datascribe:description' => $this->description(),
            'o-module-datascribe:guidelines' => $this->guidelines(),
            'o:is_public' => $this->isPublic(),
            'o-module-datascribe:project' => $this->project()->getReference(),
            'o:item_set' => $itemSet ? $itemSet->getReference() : null,
            'o-module-datascribe:synced' => $synced ? $this->getDateTime($synced) : null,
            'o-module-datascribe:synced_by' => $syncedBy ? $syncedBy->getReference() : null,
            'o:created' => $this->getDateTime($this->created()),
            'o:owner' => $owner ? $owner->getReference() : null,
        ];
    }

    public function adminUrl($action = null, $canonical = false)
    {
        $url = $this->getViewHelper('Url');
        return $url(
            'admin/datascribe-dataset-id',
            [
                'action' => $action,
                'project-id' => $this->resource->getProject()->getId(),
                'dataset-id' => $this->resource->getId(),
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

    public function guidelines()
    {
        return $this->resource->getGuidelines();
    }

    public function isPublic()
    {
        return $this->resource->getIsPublic();
    }

    public function project()
    {
        return $this->getAdapter('datascribe_projects')
            ->getRepresentation($this->resource->getProject());
    }

    public function itemSet()
    {
        return $this->getAdapter('item_sets')
            ->getRepresentation($this->resource->getItemSet());
    }

    public function synced()
    {
        return $this->resource->getSynced();
    }

    public function syncedBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getSyncedBy());
    }

    public function owner()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getOwner());
    }

    public function created()
    {
        return $this->resource->getCreated();
    }

}
