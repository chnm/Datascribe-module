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
    use TraitCreatedOwner;
    use TraitApprovedApprovedBy;

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
    protected $isApproved;

    public function setItem(DatascribeItem $item) : void
    {
        $this->item = $item;
    }

    public function getItem() : DatascribeItem
    {
        return $this->item;
    }

    public function setIsApproved(?bool $isApproved) : void
    {
        $this->isApproved = $isApproved;
    }

    public function getIsApproved() : ?bool
    {
        return $this->isApproved;
    }
}
