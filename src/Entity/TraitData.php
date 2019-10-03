<?php
namespace Datascribe\Entity;

trait TraitData
{
    /**
     * @Column(
     *     type="json",
     *     nullable=false
     * )
     */
    protected $data;

    public function setData(array $data) : void
    {
        $this->data = $data;
    }

    public function getData() : array
    {
        return $this->data;
    }
}
