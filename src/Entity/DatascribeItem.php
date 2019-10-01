<?php
namespace Datascribe\Entity;

use DateTime;
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
    use TraitSynced;
    use TraitApprovedApprovedBy;

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
    protected $completedBy;

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
    protected $completed;

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

    public function setCompletedBy(?User $completedBy = null) : void
    {
        $this->completedBy = $completedBy;
    }

    public function getCompletedBy() : ?User
    {
        return $this->completedBy;
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

    public function setCompleted(?DateTime $completed) : void
    {
        $this->completed = $completed;
    }

    public function getCompleted() : ?DateTime
    {
        return $this->completed;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->setSynced(new DateTime('now'));
    }
}
