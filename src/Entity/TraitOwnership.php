<?php
namespace Datascribe\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Omeka\Entity\User;

/**
 * Entities using this trait must include the @HasLifecycleCallbacks annotation.
 *
 * Note that we're not depending on Doctrine lifecycle events to set a modified
 * DateTime because Doctrine does not call @PreUpdate or @PostUpdate when the
 * computed changeset is empty. Any API adapters of entities using this trait
 * must call setModified() when hydrating an UPDATE request.
 */
trait TraitOwnership
{
    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $owner;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $createdBy;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $modifiedBy;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=false
     * )
     */
    protected $created;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $modified;

    public function setOwner(?User $owner = null) : void
    {
        $this->owner = $owner;
    }

    public function getOwner() : ?User
    {
        return $this->owner;
    }

    public function setCreatedBy(?User $createdBy = null) : void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy() : ?User
    {
        return $this->createdBy;
    }

    public function setModifiedBy(?User $modifiedBy = null) : void
    {
        $this->modifiedBy = $modifiedBy;
    }

    public function getModifiedBy() : ?User
    {
        return $this->modifiedBy;
    }

    public function setCreated(DateTime $created) : void
    {
        $this->created = $created;
    }

    public function getCreated() : DateTime
    {
        return $this->created;
    }

    public function setModified(DateTime $modified) : void
    {
        $this->modified = $modified;
    }

    public function getModified() : ?DateTime
    {
        return $this->modified;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventArgs) : void
    {
        $this->setCreated(new DateTime('now'));
    }
}
