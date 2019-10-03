<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"dataset_id", "position"}
 *         )
 *     }
 * )
 */
class DatascribeField extends AbstractEntity
{
    use TraitId;
    use TraitNameDescription;
    use TraitData;

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
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $label;

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $hint;

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

    public function setDataset(DatascribeDataset $dataset) : void
    {
        $this->dataset = $dataset;
    }

    public function getDataset() : DatascribeDataset
    {
        return $this->dataset;
    }

    public function setLabel(string $label) : void
    {
        $this->label = $label;
    }

    public function getLabel() : string
    {
        return $this->label;
    }

    public function setHint(?string $hint) : void
    {
        $this->hint = $hint;
    }

    public function getHint() : ?string
    {
        return $this->hint;
    }

    public function setPosition(int $position) : void
    {
        $this->position = $position;
    }

    public function getPosition() : int
    {
        return $this->position;
    }

    public function setIsPrimary(?bool $isPrimary) : void
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
}
