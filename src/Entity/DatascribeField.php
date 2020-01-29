<?php
namespace Datascribe\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 */
class DatascribeField extends AbstractEntity
{
    use TraitId;
    use TraitData;

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $name;

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $description;

    /**
     * @ManyToOne(
     *     targetEntity="DatascribeDataset"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $dataset;

    /**
     * @Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    protected $position;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=false,
     *     options={
     *         "default": false
     *     }
     * )
     */
    protected $isPrimary;

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $dataType;

    /**
     * @OneToMany(
     *     targetEntity="DatascribeValue",
     *     mappedBy="field",
     *     fetch="EXTRA_LAZY"
     * )
     */
    protected $values;

    public function __construct()
    {
        $this->values = new ArrayCollection;
    }

    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setDescription(?string $description) : void
    {
        if (is_string($description) && '' === trim($description)) {
            $description = null;
        }
        $this->description = $description;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDataset(DatascribeDataset $dataset) : void
    {
        $this->dataset = $dataset;
    }

    public function getDataset() : DatascribeDataset
    {
        return $this->dataset;
    }

    public function setPosition(int $position) : void
    {
        $this->position = $position;
    }

    public function getPosition() : int
    {
        return $this->position;
    }

    public function setIsPrimary(bool $isPrimary) : void
    {
        $this->isPrimary = $isPrimary;
    }

    public function getIsPrimary() : ?bool
    {
        return $this->isPrimary;
    }

    public function setDataType(?string $dataType) : void
    {
        $this->dataType = $dataType;
    }

    public function getDataType() : ?string
    {
        return $this->dataType;
    }

    public function getValues()
    {
        return $this->values;
    }
}
