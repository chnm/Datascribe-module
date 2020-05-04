<?php
namespace Datascribe\Form;

use Datascribe\Api\Representation\DatascribeItemRepresentation;
use Datascribe\Api\Representation\DatascribeDatasetRepresentation;
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
     * Get users who have done something to items in a dataset.
     *
     * @param string $byColumn
     * @param DatascribeDatasetRepresentation $dataset
     * @return string
     */
    protected function getByUsersForItems(string $byColumn, DatascribeDatasetRepresentation $dataset)
    {
        if (!in_array($byColumn, ['lockedBy', 'submittedBy', 'reviewedBy'])) {
            return [];
        }
        $dql = "
            SELECT u
            FROM Omeka\Entity\User u
            JOIN Datascribe\Entity\DatascribeItem i WITH i.$byColumn = u
            JOIN i.dataset d
            WHERE d = :datasetId";
        $query = $this->em->createQuery($dql);
        $query->setParameter('datasetId', $dataset->id());
        $users = $query->getResult();
        usort($users, function ($userA, $userB) {
            return strcmp($userA->getName(), $userB->getName());
        });
        return $users;
    }

    /**
     * Get users who have done something to records in a parent item or dataset.
     *
     * @param string $byColumn
     * @param DatascribeItemRepresentation|DatascribeDatasetRepresentation $parent
     * @return string
     */
    protected function getByUsersForRecords(string $byColumn, $parent)
    {
        if (!in_array($byColumn, ['createdBy', 'modifiedBy'])) {
            return [];
        }
        if ($parent instanceof DatascribeItemRepresentation) {
            $dql = "
                SELECT u
                FROM Omeka\Entity\User u
                JOIN Datascribe\Entity\DatascribeRecord r WITH r.$byColumn = u
                JOIN r.item i
                WHERE i = :parentId";
        } elseif ($parent instanceof DatascribeDatasetRepresentation) {
            $dql = "
                SELECT u
                FROM Omeka\Entity\User u
                JOIN Datascribe\Entity\DatascribeRecord r WITH r.$byColumn = u
                JOIN r.item i
                JOIN i.dataset d
                WHERE d = :parentId";
        } else {
            return [];
        }
        $query = $this->em->createQuery($dql);
        $query->setParameter('parentId', $parent->id());
        $users = $query->getResult();
        usort($users, function ($userA, $userB) {
            return strcmp($userA->getName(), $userB->getName());
        });
        return $users;
    }

    /**
     * @param array $valueOptions
     * @param DatascribeProjectRepresentation $project
     */
    protected function getLockToOtherValueOptions(array $valueOptions, DatascribeProjectRepresentation $project)
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
        foreach ($this->getProjectUsers($project) as $user) {
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
     * @param DatascribeProjectRepresentation $project
     * @return array
     */
    protected function getProjectUsers(DatascribeProjectRepresentation $project)
    {
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
