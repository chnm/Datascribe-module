<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractRepresentation;
use Datascribe\Entity\DatascribeUser;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatascribeUserRepresentation extends AbstractRepresentation
{
    protected $datascribeUser;

    public function __construct(DatascribeUser $user, ServiceLocatorInterface $services)
    {
        $this->setServiceLocator($services);
        $this->user = $user;
    }

    public function jsonSerialize()
    {
        // The project forms need the Omeka user name and email, so set the full
        // user serialization instead of just the reference.
        return [
            'o:user' => $this->user(),
            'o-module-datascribe:project' => $this->project()->getReference(),
            'o-module-datascribe:role' => $this->role(),
        ];
    }

    public function project()
    {
        return $this->getAdapter('datascribe_projects')
            ->getRepresentation($this->user->getProject());
    }

    public function user()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->user->getUser());
    }

    public function role()
    {
        return $this->user->getRole();
    }
}
