<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class DatascribeRecord extends AbstractEntity
{
    use TraitId;
    use TraitOwnership;
    use TraitNotes;

    /**
     * @ManyToOne(
     *     targetEntity="DatascribeItem"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $item;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $needsReview;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $needsWork;

    public function setItem(DatascribeItem $item) : void
    {
        $this->item = $item;
    }

    public function getItem() : DatascribeItem
    {
        return $this->item;
    }

    public function setNeedsReview(?bool $needsReview) : void
    {
        $this->needsReview = $needsReview;
    }

    public function getNeedsReview() : ?bool
    {
        return $this->needsReview;
    }

    public function setNeedsWork(?bool $needsWork) : void
    {
        $this->needsWork = $needsWork;
    }

    public function getNeedsWork() : ?bool
    {
        return $this->needsWork;
    }
}
