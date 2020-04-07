<?php
namespace Datascribe\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\ItemSet;
use Omeka\Entity\User;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class DatascribeDataset extends AbstractEntity
{
    use TraitId;
    use TraitIdentity;
    use TraitSync;
    use TraitOwnership;
    use TraitVisibility;

    /**
     * @ManyToOne(
     *     targetEntity="DatascribeProject"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $project;

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
    protected $validatedBy;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $exportedBy;

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
     *     nullable=true
     * )
     */
    protected $exportStorageId;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $validated;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $exported;

    /**
     * @OneToMany(
     *     targetEntity="DatascribeField",
     *     mappedBy="dataset",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove", "detach"},
     *     indexBy="id"
     * )
     * @OrderBy({"position" = "ASC"})
     */
    protected $fields;

    public function __construct()
    {
        $this->fields = new ArrayCollection;
    }

    public function setProject(DatascribeProject $project) : void
    {
        $this->project = $project;
    }

    public function getProject() : DatascribeProject
    {
        return $this->project;
    }

    public function setItemSet(?ItemSet $itemSet = null) : void
    {
        $this->itemSet = $itemSet;
    }

    public function getItemSet() : ?ItemSet
    {
        return $this->itemSet;
    }

    public function setValidatedBy(?User $validatedBy = null) : void
    {
        $this->validatedBy = $validatedBy;
    }

    public function getValidatedBy() : ?User
    {
        return $this->validatedBy;
    }

    public function setExportedBy(?User $exportedBy = null) : void
    {
        $this->exportedBy = $exportedBy;
    }

    public function getExportedBy() : ?User
    {
        return $this->exportedBy;
    }

    public function setGuidelines(?string $guidelines) : void
    {
        $this->guidelines = $guidelines;
    }

    public function getGuidelines() : ?string
    {
        return $this->guidelines;
    }

    public function setExportStorageId(?string $exportStorageId) : void
    {
        $this->exportStorageId = $exportStorageId;
    }

    public function getExportStorageId() : ?string
    {
        return $this->exportStorageId;
    }

    public function setValidated(DateTime $validated) : void
    {
        $this->validated = $validated;
    }

    public function getValidated() : ?DateTime
    {
        return $this->validated;
    }

    public function setExported(DateTime $exported) : void
    {
        $this->exported = $exported;
    }

    public function getExported() : ?DateTime
    {
        return $this->exported;
    }

    public function getFields()
    {
        return $this->fields;
    }
}
