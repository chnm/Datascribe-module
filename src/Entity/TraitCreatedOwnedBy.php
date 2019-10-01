<?php
namespace Datascribe\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Omeka\Entity\User;

/**
 * Entities using this trait must include the @HasLifecycleCallbacks annotation.
 */
trait TraitCreatedOwnedBy
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
    protected $ownedBy;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=false
     * )
     */
    protected $created;

    public function setOwnedBy(?User $ownedBy = null) : void
    {
        $this->ownedBy = $ownedBy;
    }

    public function getOwnedBy() : ?User
    {
        return $this->ownedBy;
    }

    public function setCreated(DateTime $created) : void
    {
        $this->created = $created;
    }

    public function getCreated() : DateTime
    {
        return $this->created;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventArgs) : void
    {
        $this->setCreated(new DateTime('now'));
    }
}
