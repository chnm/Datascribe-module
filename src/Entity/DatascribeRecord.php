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
    protected $transcriber;

    public function setItem(DatascribeItem $item) : void
    {
        $this->item = $item;
    }

    public function getItem() : DatascribeItem
    {
        return $this->item;
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
