<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeRecordRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-datascribe:Record';
    }

    public function getJsonLd()
    {
        $owner = $this->owner();
        return [
            'o-module-datascribe:item' => $this->item()->getReference(),
            'o-module-datascribe:transcriber_notes' => $this->transcriberNotes(),
            'o-module-datascribe:reviewer_notes' => $this->reviewerNotes(),
            'o-module-datascribe:needs_review' => $this->needsReview(),
            'o-module-datascribe:needs_work' => $this->needsWork(),
            'o:created' => $this->getDateTime($this->created()),
            'o:owner' => $owner ? $owner->getReference() : null,
        ];
    }

    public function adminUrl($action = null, $canonical = false)
    {
        $url = $this->getViewHelper('Url');
        $item = $this->resource->getItem();
        $dataset = $item->getDataset();
        $project = $dataset->getProject();
        return $url(
            'admin/datascribe-record-id',
            [
                'action' => $action,
                'project-id' => $project->getId(),
                'dataset-id' => $dataset->getId(),
                'item-id' => $item->getId(),
                'record-id' => $this->resource->getId(),
            ],
            ['force_canonical' => $canonical]
        );
    }

    public function item()
    {
        return $this->getAdapter('datascribe_items')
            ->getRepresentation($this->resource->getItem());
    }

    public function transcriberNotes()
    {
        return $this->resource->getTranscriberNotes();
    }

    public function reviewerNotes()
    {
        return $this->resource->getReviewerNotes();
    }

    public function needsReview()
    {
        return $this->resource->getNeedsReview();
    }

    public function needsWork()
    {
        return $this->resource->getNeedsWork();
    }

    public function created()
    {
        return $this->resource->getCreated();
    }

    public function owner()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->resource->getOwner());
    }

    public function values()
    {
        return $this->resource->getValues();
    }
}
