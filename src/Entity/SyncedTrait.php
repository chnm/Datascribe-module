<?php
namespace Datascribe\Entity;

use DateTime;

trait SyncedTrait
{
    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $synced;

    public function setSynced(?DateTime $synced) : void
    {
        $this->synced = $synced;
    }

    public function getSynced() : ?DateTime
    {
        return $this->synced;
    }
}
