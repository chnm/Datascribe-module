<?php
namespace Datascribe\Entity;

use DateTime;
use Omeka\Entity\User;

trait TraitApprovedApprovedBy
{
    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $approvedBy;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $approved;

    public function setApprovedBy(?User $approvedBy = null) : void
    {
        $this->approvedBy = $approvedBy;
    }

    public function getApprovedBy() : ?User
    {
        return $this->approvedBy;
    }

    public function setApproved(?DateTime $approved) : void
    {
        $this->approved = $approved;
    }

    public function getApproved() : ?DateTime
    {
        return $this->approved;
    }
}
