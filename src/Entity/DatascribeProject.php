<?php
namespace Datascribe\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 * @Table(
 *     uniqueConstraints={
 *         @UniqueConstraint(
 *             columns={"name"}
 *         )
 *     }
 * )
 * @HasLifecycleCallbacks
 */
class DatascribeProject extends AbstractEntity
{
    use TraitId;
    use TraitIdentity;
    use TraitOwnership;
    use TraitVisibility;

    /**
     * @OneToMany(
     *     targetEntity="DatascribeUser",
     *     mappedBy="project",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove", "detach"},
     *     indexBy="user_id"
     * )
     */
    protected $users;

    public function __construct()
    {
        $this->users = new ArrayCollection;
    }

    public function getUsers()
    {
        return $this->users;
    }
}
