<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;
use Omeka\Entity\User;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class DatascribeRecord extends AbstractEntity
{
    use IdTrait;
    use CreatedTrait;

    /**
     * @ManyToOne(
     *     targetEntity="DatascribeItem"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $datascribeItem;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $transcriber;

    public function setDatascribeItem(DatascribeProject $datascribeItem) : void
    {
        $this->datascribeItem = $datascribeItem;
    }

    public function getDatascribeItem() : DatascribeItem
    {
        return $this->datascribeItem;
    }

    public function setTranscriber(?User $transcriber = null) : void
    {
        $this->transcriber = $transcriber;
    }

    public function getTranscriber() : ?User
    {
        return $this->transcriber;
    }
}
