<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 */
class DatascribeValue extends AbstractEntity
{
    use IdTrait;

    /**
     * @ManyToOne(
     *     targetEntity="DatascribeField"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $field;

    /**
     * @ManyToOne(
     *     targetEntity="DatascribeRecord"
     * )
     * @JoinColumn(
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     */
    protected $record;

    /**
     * @Column(
     *     type="json_array",
     *     nullable=false
     * )
     */
    protected $data;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $missing;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $illegible;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $review;

    public function setField(DatascribeField $field) : void
    {
        $this->field = $field;
    }

    public function getField() : DatascribeField
    {
        return $this->field;
    }

    public function setRecord(DatascribeRecord $record) : void
    {
        $this->record = $record;
    }

    public function getRecord() : DatascribeRecord
    {
        return $this->record;
    }

    public function setData(array $data) : void
    {
        $this->data = $data;
    }

    public function getData() : array
    {
        return $this->data;
    }

    public function setMissing(?bool $missing) : void
    {
        $this->missing = $missing;
    }

    public function getMissing() : ?bool
    {
        return $this->missing;
    }

    public function setIllegible(?bool $illegible) : void
    {
        $this->illegible = $illegible;
    }

    public function getIllegible() : ?bool
    {
        return $this->illegible;
    }

    public function setReview(?bool $review) : void
    {
        $this->review = $review;
    }

    public function getReview() : ?bool
    {
        return $this->review;
    }
}
