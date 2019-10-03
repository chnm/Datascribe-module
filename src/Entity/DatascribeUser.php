<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Exception;
use Omeka\Entity\User;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"project_id", "user_id"}
 *         )
 *     }
 * )
 */
class DatascribeUser extends AbstractEntity
{
    use TraitId;

    const ROLE_REVIEWER = 'reviewer';
    const ROLE_TRANSCRIBER = 'transcriber';

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
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $user;

    /**
     * @Column(
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $role;

    public function setProject(DatascribeProject $project) : void
    {
        $this->project = $project;
    }

    public function getProject() : DatascribeProject
    {
        return $this->project;
    }

    public function setUser(?User $user = null) : void
    {
        $this->user = $user;
    }

    public function getUser() : ?User
    {
        return $this->user;
    }

    public function setRole(string $role) : void
    {
        if (!in_array($role, [self::ROLE_REVIEWER, self::ROLE_TRANSCRIBER])) {
            throw new Exception\InvalidArgumentException('Invalid user role');
        }
        $this->role = $role;
    }

    public function getRole() : string
    {
        return $this->role;
    }
}
