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
 *             columns={"project_id", "item_id"}
 *         )
 *     }
 * )
 * @HasLifecycleCallbacks
 */
class DatascribeItem extends AbstractEntity
{
    use IdTrait;
    use SyncedTrait;

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
    protected $completedBy;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $approvedBy;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $completed;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $approved;

    public function setProject(DatascribeProject $project) : void
    {
        $this->project = $project;
    }

    public function getProject() : DatascribeProject
    {
        return $this->project;
    }

    public function setItem(Item $item) : void
    {
        $this->item = $item;
    }

    public function getItem() : Item
    {
        return $this->item;
    }

    public function setCompletedBy(?User $completedBy = null) : void
    {
        $this->completedBy = $completedBy;
    }

    public function getCompletedBy() : ?User
    {
        return $this->completedBy;
    }

    public function setApprovedBy(?User $approvedBy = null) : void
    {
        $this->approvedBy = $approvedBy;
    }

    public function getApprovedBy() : ?User
    {
        return $this->approvedBy;
    }

    public function setCompleted(?DateTime $completed) : void
    {
        $this->completed = $completed;
    }

    public function getCompleted() : ?DateTime
    {
        return $this->completed;
    }

    public function setApproved(?DateTime $approved) : void
    {
        $this->approved = $approved;
    }

    public function getApproved() : ?DateTime
    {
        return $this->approved;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->setSynced(new DateTime('now'));
    }
}
