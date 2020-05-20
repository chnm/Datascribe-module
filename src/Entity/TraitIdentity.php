<?php
namespace Datascribe\Entity;

trait TraitIdentity
{
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
}
