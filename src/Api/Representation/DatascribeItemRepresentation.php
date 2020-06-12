<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeItemRepresentation extends AbstractEntityRepresentation
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_NEEDS_REVIEW = 'needs_review';
    const STATUS_NOT_APPROVED = 'not_approved';
    const STATUS_APPROVED = 'approved';
    const STATUS_UNKNOWN = 'unknown';

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
        $submitted = $this->submitted();
        $submittedBy = $this->submittedBy();
        $reviewed = $this->reviewed();
        $reviewedBy = $this->reviewedBy();
        return [
            'o-module-datascribe:dataset' => $this->dataset()->getReference(),
            'o-module-datascribe:item' => $this->item()->getReference(),
            'o-module-datascribe:synced' => $synced ? $this->getDateTime($synced) : null,
            'o-module-datascribe:synced_by' => $syncedBy ? $syncedBy->getReference() : null,
            'o-module-datascribe:prioritized' => $prioritized ? $this->getDateTime($prioritized) : null,
            'o-module-datascribe:prioritized_by' => $prioritizedBy ? $prioritizedBy->getReference() : null,
            'o-module-datascribe:locked' => $locked ? $this->getDateTime($locked) : null,
            'o-module-datascribe:locked_by' => $lockedBy ? $lockedBy->getReference() : null,
            'o-module-datascribe:submitted' => $submitted ? $this->getDateTime($submitted) : null,
            'o-module-datascribe:submitted_by' => $submittedBy ? $submittedBy->getReference() : null,
            'o-module-datascribe:is_approved' => $this->isApproved(),
            'o-module-datascribe:reviewed' => $reviewed ? $this->getDateTime($reviewed) : null,
            'o-module-datascribe:reviewed_by' => $reviewedBy ? $reviewedBy->getReference() : null,
            'o-module-datascribe:transcriber_notes' => $this->transcriberNotes(),
            'o-module-datascribe:reviewer_notes' => $this->reviewerNotes(),
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

    public function submitted()
    {
        return $this->resource->getSubmitted();
    }

    public function submittedBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getSubmittedBy());
    }

    public function isApproved()
    {
        return $this->resource->getIsApproved();
    }

    public function reviewed()
    {
        return $this->resource->getReviewed();
    }

    public function reviewedBy()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getReviewedBy());
    }

    public function transcriberNotes()
    {
        return $this->resource->getTranscriberNotes();
    }

    public function reviewerNotes()
    {
        return $this->resource->getReviewerNotes();
    }

    public function recordCount()
    {
        return $this->resource->getRecords()->count();
    }

    public function records()
    {
        $records = [];
        $recordAdapter = $this->getAdapter('datascribe_records');
        foreach ($this->resource->getRecords() as $recordEntity) {
            $records[] = $recordAdapter->getRepresentation($recordEntity);
        }
        return $records;
    }

    public function status()
    {
        if (null === $this->submitted()
            && null === $this->reviewed()
            && null === $this->isApproved()
            && 0 === $this->recordCount()
        ) {
            return self::STATUS_NEW;
        }
        if (null === $this->submitted()
            && null === $this->reviewed()
            && null === $this->isApproved()
            && 0 < $this->recordCount()
        ) {
            return self::STATUS_IN_PROGRESS;
        }
        if (null !== $this->submitted()
            && null === $this->reviewed()
            && null === $this->isApproved()
        ) {
            return self::STATUS_NEEDS_REVIEW;
        }
        if (null !== $this->submitted()
            && null !== $this->reviewed()
            && ($this->submitted() > $this->reviewed())
            && false === $this->isApproved()
        ) {
            return self::STATUS_NEEDS_REVIEW;
        }
        if (null !== $this->submitted()
            && null !== $this->reviewed()
            && ($this->submitted() < $this->reviewed())
            && false === $this->isApproved()
        ) {
            return self::STATUS_NOT_APPROVED;
        }
        if (null === $this->submitted()
            && false === $this->isApproved()
        ) {
            return self::STATUS_NOT_APPROVED;
        }
        if (true === $this->isApproved()) {
            return self::STATUS_APPROVED;
        }
        return self::STATUS_UNKNOWN;
    }

    public function statusLabel()
    {
        switch ($this->status()) {
            case self::STATUS_NEW:
                return 'New'; // @translate
            case self::STATUS_IN_PROGRESS:
                return 'In progress'; // @translate
            case self::STATUS_NEEDS_REVIEW:
                return 'Needs review'; // @translate
            case self::STATUS_NOT_APPROVED:
                return 'Not approved'; // @translate
            case self::STATUS_APPROVED:
                return 'Approved'; // @translate
            case self::STATUS_UNKNOWN:
            default:
                return '[Unknown]'; // @translate
        }
    }

    public function hasInvalidValues()
    {
        return (bool) $this->getAdapter()->getInvalidValueCount($this->resource);
    }

    public function hasNeedsReviewRecords()
    {
        return (bool) $this->getAdapter()->getNeedsReviewCount($this->resource);
    }

    public function hasNeedsWorkRecords()
    {
        return (bool) $this->getAdapter()->getNeedsWorkCount($this->resource);
    }

}
