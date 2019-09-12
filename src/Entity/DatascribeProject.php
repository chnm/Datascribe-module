<?php
namespace Datascribe\Entity;

use DateTime;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\ItemSet;
use Omeka\Entity\User;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class DatascribeProject extends AbstractEntity
{
    use SyncedTrait;
    use CreatedTrait;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\ItemSet"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $itemSet;

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
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $title;

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $description;

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $guidelines;

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $recordLabel;

    public function getId()
    {
        return $this->id;
    }

    public function setItemSet(?ItemSet $itemSet = null) : void
    {
        $this->itemSet = $itemSet;
    }

    public function getItemSet() : ?ItemSet
    {
        return $this->itemSet;
    }

    public function setOwner(?User $owner = null) : void
    {
        $this->owner = $owner;
    }

    public function getOwner() : ?User
    {
        return $this->owner;
    }

    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setGuidelines(?string $guidelines) : void
    {
        $this->guidelines = $guidelines;
    }

    public function getGuidelines() : ?string
    {
        return $this->guidelines;
    }

    public function setRecordLabel(string $recordLabel) : void
    {
        $this->recordLabel = $recordLabel;
    }

    public function getRecordLabel() : string
    {
        return $this->recordLabel;
    }
}
