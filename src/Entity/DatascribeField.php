<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"project_id", "position"}
 *         )
 *     }
 * )
 */
class DatascribeField extends AbstractEntity
{
    use IdTrait;

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
    protected $description;

    /**
     * @Column(
     *     type="integer",
     *     nullable=false
     * )
     */
    protected $position;

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $dataType;

    /**
     * @Column(
     *     type="json_array",
     *     nullable=false
     * )
     */
    protected $data;

    public function setProject(DatascribeProject $project) : void
    {
        $this->project = $project;
    }

    public function getProject() : DatascribeProject
    {
        return $this->project;
    }

    public function setLabel(string $label) : void
    {
        $this->label = $label;
    }

    public function getLabel() : string
    {
        return $this->label;
    }

    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setPosition(int $position) : void
    {
        $this->position = $position;
    }

    public function getPosition() : int
    {
        return $this->position;
    }

    public function setDataType(?string $dataType) : void
    {
        $this->dataType = $dataType;
    }

    public function getDataType() : ?string
    {
        return $this->dataType;
    }

    public function setData(array $data) : void
    {
        $this->data = $data;
    }

    public function getData() : array
    {
        return $this->data;
    }
}
