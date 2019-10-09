<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeItemRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-datascribe:Item';
    }

    public function getJsonLd()
    {
        $synced = $this->synced();
        $syncedBy = $this->syncedBy();
        $prioritized = $this->prioritized();
        $prioritizedBy = $this->prioritizedBy();
        $locked = $this->locked();
        $lockedBy = $this->lockedBy();
        $completed = $this->completed();
        $completedBy = $this->completedBy();
        $approved = $this->approved();
        $approvedBy = $this->approvedBy();
        return [
            'o-module-datascribe:dataset' => $this->dataset()->getReference(),
            'o-module-datascribe:item' => $this->item()->getReference(),
            'o-module-datascribe:synced' => $synced ? $this->getDateTime($synced) : null,
            'o-module-datascribe:synced_by' => $syncedBy ? $syncedBy->getReference() : null,
            'o-module-datascribe:prioritized' => $prioritized ? $this->getDateTime($prioritized) : null,
            'o-module-datascribe:prioritized_by' => $prioritizedBy ? $prioritizedBy->getReference() : null,
            'o-module-datascribe:locked' => $locked ? $this->getDateTime($locked) : null,
            'o-module-datascribe:locked_by' => $lockedBy ? $lockedBy->getReference() : null,
            'o-module-datascribe:completed' => $completed ? $this->getDateTime($completed) : null,
            'o-module-datascribe:completed_by' => $completedBy ? $completedBy->getReference() : null,
            'o-module-datascribe:approved' => $approved ? $this->getDateTime($approved) : null,
            'o-module-datascribe:approved_by' => $approvedBy ? $approvedBy->getReference() : null,
        ];
    }

    public function adminUrl($action = null, $canonical = false)
    {
        $url = $this->getViewHelper('Url');
        $dataset = $this->resource->getDataset();
        $project = $dataset->getProject();
        return $url(
            'admin/datascribe-item-id',
            [
                'action' => $action,
                'project-id' => $project->getId(),
                'dataset-id' => $dataset->getId(),
                'item-id' => $this->resource->getId(),
            ],
            ['force_canonical' => $canonical]
        );
    }

    public function linkPretty($thumbnailType = 'square', $titleDefault = null,
        $action = null, array $attributes = null
    ) {
        $item = $this->item();
        $escape = $this->getViewHelper('escapeHtml');
        $thumbnail = $this->getViewHelper('thumbnail');
        $linkContent = sprintf(
            '%s<span class="resource-name">%s</span>',
            $thumbnail($item, $thumbnailType),
            $escape($item->displayTitle($titleDefault))
        );
        if (empty($attributes['class'])) {
            $attributes['class'] = 'resource-link';
        } else {
            $attributes['class'] .= ' resource-link';
        }
        return $this->linkRaw($linkContent, $action, $attributes);
    }

    public function dataset()
    {
        return $this->getAdapter('datascribe_datasets')
            ->getRepresentation($this->resource->getDataset());
    }

    public function item()
    {
        return $this->getAdapter('items')
            ->getRepresentation($this->resource->getItem());
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

    public function prioritized()
    {
        return $this->resource->getPrioritized();
    }

    public function prioritizedBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getPrioritizedBy());
    }

    public function locked()
    {
        return $this->resource->getLocked();
    }

    public function lockedBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getLockedBy());
    }

    public function completed()
    {
        return $this->resource->getCompleted();
    }

    public function completedBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getCompletedBy());
    }

    public function approved()
    {
        return $this->resource->getApproved();
    }

    public function approvedBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getApprovedBy());
    }
}
