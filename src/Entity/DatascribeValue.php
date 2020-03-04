<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"field_id", "record_id"}
 *         )
 *     }
 * )
 */
class DatascribeValue extends AbstractEntity
{
    use TraitId;

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
     *     nullable=false,
     *     options={"default":0}
     * )
     */
    protected $isInvalid;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=false,
     *     options={"default":0}
     * )
     */
    protected $isMissing;

    /**
     * @Column(
     *     type="boolean",
     *     nullable=false,
     *     options={"default":0}
     * )
     */
    protected $isIllegible;

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $text;

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

    public function setIsInvalid(bool $isInvalid) : void
    {
        $this->isInvalid = $isInvalid;
    }

    public function getIsInvalid() : bool
    {
        return $this->isInvalid;
    }

    public function setIsMissing(bool $isMissing) : void
    {
        $this->isMissing = $isMissing;
    }

    public function getIsMissing() : bool
    {
        return $this->isMissing;
    }

    public function setIsIllegible(bool $isIllegible) : void
    {
        $this->isIllegible = $isIllegible;
    }

    public function getIsIllegible() : bool
    {
        return $this->isIllegible;
    }

    public function setText(?string $text) : void
    {
        $this->text = $text;
    }

    public function getText() : ?string
    {
        return $this->text;
    }
}
