<?php
namespace Datascribe\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"item_id", "position"}
 *         )
 *     }
 * )
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
     *     nullable=false,
     *     options={"default":0}
     * )
     */
    protected $needsReview;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=false,
     *     options={"default":0}
     * )
     */
    protected $needsWork;

    /**
     * @Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    protected $position;

    protected $newPosition;

    /**
     * @OneToMany(
     *     targetEntity="DatascribeValue",
     *     mappedBy="record",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove", "detach"},
     *     indexBy="field_id"
     * )
     */
    protected $values;

    public function __construct()
    {
        $this->values = new ArrayCollection;
    }

    public function setItem(DatascribeItem $item) : void
    {
        $this->item = $item;
    }

    public function getItem() : DatascribeItem
    {
        return $this->item;
    }

    public function setNeedsReview(bool $needsReview) : void
    {
        $this->needsReview = $needsReview;
    }

    public function getNeedsReview() : bool
    {
        return $this->needsReview;
    }

    public function setNeedsWork(bool $needsWork) : void
    {
        $this->needsWork = $needsWork;
    }

    public function getNeedsWork() : bool
    {
        return $this->needsWork;
    }

    public function setPosition(int $position) : void
    {
        $this->position = $position;
    }

    public function getPosition() : int
    {
        return $this->position;
    }

    public function setNewPosition(int $newPosition) : void
    {
        $this->newPosition = $newPosition;
    }

    public function getNewPosition() : ?int
    {
        return $this->newPosition;
    }

    public function getValues()
    {
        return $this->values;
    }
}
