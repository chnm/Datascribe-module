<?php
namespace Datascribe\Entity;

use DateTime;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Item;
use Omeka\Entity\User;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"datascribe_project_id", "omeka_item_id"}
 *         )
 *     }
 * )
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
    protected $datascribeProject;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\Item"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $omekaItem;

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

    public function setDatascribeProject(DatascribeProject $datascribeProject) : void
    {
        $this->datascribeProject = $datascribeProject;
    }

    public function getDatascribeProject() : DatascribeProject
    {
        return $this->datascribeProject;
    }

    public function setOmekaItem(Item $omekaItem) : void
    {
        $this->omekaItem = $omekaItem;
    }

    public function getOmekaItem() : Item
    {
        return $this->omekaItem;
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
}
