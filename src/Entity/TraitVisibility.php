<?php
namespace Datascribe\Entity;

trait TraitVisibility
{
    /**
     * @Column(
     *     type="boolean",
     *     nullable=false,
     *     options={
     *         "default": false
     *     }
     * )
     */
    protected $isPublic;

    public function setIsPublic(bool $isPublic) : void
    {
        $this->isPublic = $isPublic;
    }

    public function getIsPublic() : bool
    {
        return $this->isPublic;
    }
}
