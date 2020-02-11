<?php
namespace Datascribe\Form;

use Datascribe\Api\Representation\DatascribeProjectRepresentation;
use Datascribe\Entity\DatascribeUser;
use Doctrine\ORM\EntityManager;
use Omeka\Entity\User;
use Zend\Form\Form;

abstract class AbstractItemForm extends Form
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Set the Doctrine entity manager.
     *
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function getLockToOtherValueOptions(array $valueOptions)
    {
        $valueOptions['transcribers'] = [
            'label' => 'Lock to transcriber', // @translate
            'options' => [],
        ];
        $valueOptions['reviewers'] = [
            'label' => 'Lock to reviewer', // @translate
            'options' => [],
        ];
        $valueOptions['admins'] = [
            'label' => 'Lock to admin', // @translate
            'options' => [],
        ];
        foreach ($this->getAdminUsers() as $user) {
            $valueOptions['admins']['options'][$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
        }
        foreach ($this->getProjectUsers() as $user) {
            $oUser = $user->getUser();
            if (DatascribeUser::ROLE_REVIEWER === $user->getRole()) {
                $valueOptions['reviewers']['options'][$oUser->getId()] = sprintf('%s (%s)', $oUser->getName(), $oUser->getEmail());
            } elseif (DatascribeUser::ROLE_TRANSCRIBER === $user->getRole()) {
                $valueOptions['transcribers']['options'][$oUser->getId()] = sprintf('%s (%s)', $oUser->getName(), $oUser->getEmail());
            }
        }
        return $valueOptions;
    }

    /**
     * Get all administrative users of Omeka.
     *
     * @return array
     */
    protected function getAdminUsers()
    {
        $dql = "
        SELECT u
        FROM Omeka\Entity\User u
        WHERE u.role = 'global_admin'
        OR u.role = 'site_admin'
        ORDER BY u.name";
        $query = $this->em->createQuery($dql);
        return $query->getResult();
    }

    /**
     * Get all users of the configured project.
     * 
     * @return array
     */
    protected function getProjectUsers()
    {
        $project = $this->getOption('project');
        $dql = "
        SELECT u
        FROM Datascribe\Entity\DatascribeUser u
        JOIN u.project p
        JOIN u.user ou
        WHERE p = :projectId
        ORDER BY ou.name";
        $query = $this->em->createQuery($dql);
        $query->setParameter('projectId', $project->id());
        return $query->getResult();
    }
}
