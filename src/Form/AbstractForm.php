<?php
namespace Datascribe\Form;

use Datascribe\Api\Representation\DatascribeProjectRepresentation;
use Datascribe\Entity\DatascribeUser;
use Doctrine\ORM\EntityManager;
use Omeka\Entity\User;
use Zend\Form\Form;

abstract class AbstractForm extends Form
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

    /**
     * Get project users for the value options.
     *
     * This will only get users who are set in the $byColumn.
     *
     * @param DatascribeProjectRepresentation $project
     * @param string $byColumn
     * @return string
     */
    protected function getByUsers(string $byColumn, DatascribeProjectRepresentation $project)
    {
        if (!in_array($byColumn, ['lockedBy', 'submittedBy', 'reviewedBy'])) {
            return [];
        }
        $dql = "
            SELECT u
            FROM Omeka\Entity\User u
            JOIN Datascribe\Entity\DatascribeItem i WITH i.$byColumn = u
            JOIN i.dataset d
            JOIN d.project p
            WHERE p = :projectId";
        $query = $this->em->createQuery($dql);
        $query->setParameter('projectId', $project->id());
        $users = $query->getResult();
        usort($users, function ($userA, $userB) {
            return strcmp($userA->getName(), $userB->getName());
        });
        return $users;
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
