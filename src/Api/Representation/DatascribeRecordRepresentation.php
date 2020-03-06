<?php
namespace Datascribe\Api\Representation;

use Doctrine\Common\Collections\Criteria;
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
        $createdBy = $this->createdBy();
        $modifiedBy = $this->modifiedBy();
        $modified = $this->modified();
        return [
            'o-module-datascribe:item' => $this->item()->getReference(),
            'o-module-datascribe:transcriber_notes' => $this->transcriberNotes(),
            'o-module-datascribe:reviewer_notes' => $this->reviewerNotes(),
            'o-module-datascribe:needs_review' => $this->needsReview(),
            'o-module-datascribe:needs_work' => $this->needsWork(),
            'o:created' => $this->getDateTime($this->created()),
            'o:modified' => $modified ? $this->getDateTime($modified) : null,
            'o:owner' => $owner ? $owner->getReference() : null,
            'o-module-datascribe:created_by' => $createdBy ? $createdBy->getReference() : null,
            'o-module-datascribe:modified_by' => $modifiedBy ? $modifiedBy->getReference() : null,
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

    public function values()
    {
        $values = [];
        foreach ($this->resource->getValues() as $fieldId => $valueEntity) {
            $values[$fieldId] = new DatascribeValueRepresentation($valueEntity, $this->getServiceLocator());
        }
        return $values;
    }

    public function primaryValue()
    {
        // Get the primary field.
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isPrimary', true))
            ->setMaxResults(1);
        $dataset = $this->resource->getItem()->getDataset();
        $primaryFieldEntity = $dataset->getFields()->matching($criteria)->first();
        if (!$primaryFieldEntity) {
            return null; // no primary field
        }
        // Get the primary value.
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('field', $primaryFieldEntity))
            ->setMaxResults(1);
        $primaryValueEntity = $this->resource->getValues()->matching($criteria)->first();
        if (!$primaryValueEntity) {
            return null; // no primary value
        }
        return new DatascribeValueRepresentation($primaryValueEntity, $this->getServiceLocator());
    }

    public function hasInvalidValues()
    {
        return (bool) $this->getAdapter()->getInvalidValueCount($this->resource);
    }

    public function displayTitle()
    {
        $translator = $this->getTranslator();
        $primaryValue = $this->primaryValue();
        if ($primaryValue) {
            $displayTitle = sprintf(
                $translator->translate('Record #%s: %s'),
                $this->id(),
                $primaryValue->displayText(['length' => 25, 'if_invalid_return' => null, 'if_empty_return' => null])
            );
        } else {
            $displayTitle = sprintf($translator->translate('Record #%s'), $this->id());
        }
        return $displayTitle;
    }
}
