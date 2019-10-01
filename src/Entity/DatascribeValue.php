<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 */
class DatascribeValue extends AbstractEntity
{
    use TraitId;
    use TraitData;

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
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $isInvalid;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $isMissing;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $isIllegible;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $needsReview;

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

    public function setIsInvalid(?bool $isInvalid) : void
    {
        $this->isInvalid = $isInvalid;
    }

    public function getIsInvalid() : ?bool
    {
        return $this->isInvalid;
    }

    public function setIsMissing(?bool $isMissing) : void
    {
        $this->isMissing = $isMissing;
    }

    public function getIsMissing() : ?bool
    {
        return $this->isMissing;
    }

    public function setIsIllegible(?bool $isIllegible) : void
    {
        $this->isIllegible = $isIllegible;
    }

    public function getIsIllegible() : ?bool
    {
        return $this->isIllegible;
    }

    public function setNeedsReview(?bool $needsReview) : void
    {
        $this->needsReview = $needsReview;
    }

    public function getNeedsReview() : ?bool
    {
        return $this->needsReview;
    }
}
