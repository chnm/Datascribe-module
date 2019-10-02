<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractRepresentation;
use Datascribe\Entity\DatascribeUser;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatascribeUserRepresentation extends AbstractRepresentation
{
    protected $datascribeUser;

    public function __construct(DatascribeUser $datascribeUser, ServiceLocatorInterface $services)
    {
        $this->setServiceLocator($services);
        $this->datascribeUser = $datascribeUser;
    }

    public function jsonSerialize()
    {
        return [
            'o:user' => $this->user()->getReference(),
            'o-module-datascribe:project' => $this->project()->getReference(),
            'o-module-datascribe:role' => $this->role(),
        ];
    }

    public function project()
    {
        return $this->getAdapter('datascribe_projects')
            ->getRepresentation($this->datascribeUser->getDatascribeProject());
    }

    public function user()
    {
        return $this->getAdapter('users')
            ->getRepresentation($this->datascribeUser->getUser());
    }

    public function role()
    {
        return $this->datascribeUser->getRole();
    }
}
