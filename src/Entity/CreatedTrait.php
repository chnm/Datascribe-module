<?php
namespace Datascribe\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Entities using this trait must include the @HasLifecycleCallbacks annotation.
 */
trait CreatedTrait
{
    /**
     * @Column(
     *     type="datetime",
     *     nullable=false
     * )
     */
    protected $created;

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
