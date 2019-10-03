<?php
namespace Datascribe\Entity;

use DateTime;
use Omeka\Entity\User;

trait TraitSyncedSyncedBy
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
    protected $syncedBy;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $synced;

    public function setSyncedBy(?User $syncedBy = null) : void
    {
        $this->syncedBy = $syncedBy;
    }

    public function getSyncedBy() : ?User
    {
        return $this->syncedBy;
    }

    public function setSynced(?DateTime $synced) : void
    {
        $this->synced = $synced;
    }

    public function getSynced() : ?DateTime
    {
        return $this->synced;
    }
}
