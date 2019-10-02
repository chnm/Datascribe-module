<?php
namespace Datascribe\Entity;

use DateTime;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\ItemSet;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class DatascribeDataset extends AbstractEntity
{
    use TraitId;
    use TraitNameDescription;
    use TraitSynced;
    use TraitCreatedOwner;

    /**
     * @ManyToOne(
     *     targetEntity="DatascribeProject"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $project;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\ItemSet"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $itemSet;

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $guidelines;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $isPublic;

    public function setProject(DatascribeProject $project) : void
    {
        $this->project = $project;
    }

    public function getProject() : DatascribeProject
    {
        return $this->project;
    }

    public function setItemSet(?ItemSet $itemSet = null) : void
    {
        $this->itemSet = $itemSet;
    }

    public function getItemSet() : ?ItemSet
    {
        return $this->itemSet;
    }

    public function setGuidelines(?string $guidelines) : void
    {
        $this->guidelines = $guidelines;
    }

    public function getGuidelines() : ?string
    {
        return $this->guidelines;
    }

    public function setIsPublic(bool $isPublic) : void
    {
        $this->isPublic = $isPublic;
    }

    public function getIsPublic() : bool
    {
        return $this->isPublic;
    }
}
