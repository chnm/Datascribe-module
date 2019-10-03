<?php
namespace Datascribe\Entity;

trait TraitNameDescription
{
    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false,
     *     unique=true
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
        $this->description = $description;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }
}
