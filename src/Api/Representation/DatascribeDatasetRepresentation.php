<?php
namespace Datascribe\Api\Representation;

use Doctrine\Common\Collections\Criteria;
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
        $createdBy = $this->createdBy();
        $modifiedBy = $this->modifiedBy();
        $modified = $this->modified();
        $synced = $this->synced();
        $syncedBy = $this->syncedBy();
        $itemSet = $this->itemSet();
        return [
            'o-module-datascribe:project' => $this->project()->getReference(),
            'o-module-datascribe:name' => $this->name(),
            'o-module-datascribe:description' => $this->description(),
            'o-module-datascribe:guidelines' => $this->guidelines(),
            'o:is_public' => $this->isPublic(),
            'o:item_set' => $itemSet ? $itemSet->getReference() : null,
            'o-module-datascribe:synced' => $synced ? $this->getDateTime($synced) : null,
            'o-module-datascribe:synced_by' => $syncedBy ? $syncedBy->getReference() : null,
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

    public function created()
    {
        return $this->resource->getCreated();
    }

    public function modified()
    {
        return $this->resource->getModified();
    }

    public function fields(array $options = [])
    {
        // Set default options.
        $options['primary_first'] = $options['primary_first'] ?? false;
        $options['exclude_primary'] = $options['exclude_primary'] ?? false;

        // Filter/sort fields.
        if (true === $options['primary_first']) {
            $criteria = Criteria::create()
                ->orderBy(['isPrimary' => Criteria::DESC, 'position' => Criteria::ASC]);
        } elseif (true === $options['exclude_primary']) {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->eq('isPrimary', false));
        } else {
            $criteria = Criteria::create();
        }
        $fieldCollection = $this->resource->getFields()->matching($criteria);

        // Set field representations.
        $fields = [];
        foreach ($fieldCollection as $fieldEntity) {
            $fields[] = new DatascribeFieldRepresentation($fieldEntity, $this->getServiceLocator());
        }
        return $fields;
    }
}
