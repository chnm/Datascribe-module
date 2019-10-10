<?php
namespace Datascribe\Entity;

use DateTime;
use Omeka\Entity\User;

trait TraitApproval
{
    /**
     * @Column(
     *     type="boolean",
     *     nullable=true
     * )
     */
    protected $isApproved;

    /**
     * @Column(
     *     type="datetime",
     *     nullable=true
     * )
     */
    protected $approved;

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

    public function setIsApproved(?bool $isApproved) : void
    {
        $this->isApproved = $isApproved;
    }

    public function getIsApproved() : ?bool
    {
        return $this->isApproved;
    }

    public function setApproved(?DateTime $approved) : void
    {
        $this->approved = $approved;
    }

    public function getApproved() : ?DateTime
    {
        return $this->approved;
    }

    public function setApprovedBy(?User $approvedBy = null) : void
    {
        $this->approvedBy = $approvedBy;
    }

    public function getApprovedBy() : ?User
    {
        return $this->approvedBy;
    }
}
