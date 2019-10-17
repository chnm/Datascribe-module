<?php
namespace Datascribe\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Item;
use Omeka\Entity\User;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"dataset_id", "item_id"}
 *         )
 *     }
 * )
 * @HasLifecycleCallbacks
 */
class DatascribeItem extends AbstractEntity
{
    use TraitId;
    use TraitSync;
    use TraitNotes;

    /**
     * @ManyToOne(
     *     targetEntity="DatascribeDataset"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $dataset;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\Item"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $item;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $prioritizedBy;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $lockedBy;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $submittedBy;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $reviewedBy;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $prioritized;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $locked;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $submitted;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $reviewed;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $isApproved;

    /**
     * @OneToMany(
     *     targetEntity="DatascribeRecord",
     *     mappedBy="item",
     *     fetch="EXTRA_LAZY"
     * )
     */
    protected $records;

    public function __construct()
    {
        $this->users = new ArrayCollection;
    }

    public function setDataset(DatascribeDataset $dataset) : void
    {
        $this->dataset = $dataset;
    }

    public function getDataset() : DatascribeDataset
    {
        return $this->dataset;
    }

    public function setItem(Item $item) : void
    {
        $this->item = $item;
    }

    public function getItem() : Item
    {
        return $this->item;
    }

    public function setPrioritizedBy(?User $prioritizedBy = null) : void
    {
        $this->prioritizedBy = $prioritizedBy;
    }

    public function getPrioritizedBy() : ?User
    {
        return $this->prioritizedBy;
    }

    public function setLockedBy(?User $lockedBy = null) : void
    {
        $this->lockedBy = $lockedBy;
    }

    public function getLockedBy() : ?User
    {
        return $this->lockedBy;
    }

    public function setSubmittedBy(?User $submittedBy = null) : void
    {
        $this->submittedBy = $submittedBy;
    }

    public function getSubmittedBy() : ?User
    {
        return $this->submittedBy;
    }

    public function setReviewedBy(?User $reviewedBy = null) : void
    {
        $this->reviewedBy = $reviewedBy;
    }

    public function getReviewedBy() : ?User
    {
        return $this->reviewedBy;
    }

    public function setPrioritized(?DateTime $prioritized) : void
    {
        $this->prioritized = $prioritized;
    }

    public function getPrioritized() : ?DateTime
    {
        return $this->prioritized;
    }

    public function setLocked(?DateTime $locked) : void
    {
        $this->locked = $locked;
    }

    public function getLocked() : ?DateTime
    {
        return $this->locked;
    }

    public function setSubmitted(?DateTime $submitted) : void
    {
        $this->submitted = $submitted;
    }

    public function getSubmitted() : ?DateTime
    {
        return $this->submitted;
    }

    public function setReviewed(?DateTime $reviewed) : void
    {
        $this->reviewed = $reviewed;
    }

    public function getReviewed() : ?DateTime
    {
        return $this->reviewed;
    }

    public function setIsApproved(?bool $isApproved) : void
    {
        $this->isApproved = $isApproved;
    }

    public function getIsApproved() : ?bool
    {
        return $this->isApproved;
    }

    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->setSynced(new DateTime('now'));
    }
}
