<?php
namespace Datascribe\Form;

use Doctrine\ORM\EntityManager;
use Omeka\Form\Element\UserSelect;
use Zend\Form\Form;

class ItemSearchForm extends Form
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function init()
    {
        $this->add([
            'type' => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Status', // @translate
                'empty_option' => '',
                'value_options' => [
                    'new' => 'New', // @translate
                    'in_progress' => 'In progress', // @translate
                    'need_review' => 'Need review', // @translate
                    'not_approved' => 'Not approved', // @translate
                    'approved' => 'Approved', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select status', // @translate
            ],
        ]);
        $valueOptions = [
            'not_submitted' => 'Not submitted', // @translate
            'submitted' => 'Submitted', // @translate
        ];
        foreach ($this->getByUsers('submittedBy', $this->getOption('project_id')) as $user) {
            $valueOptions[$user->getId()] = sprintf('Submitted by %s (%s)', $user->getName(), $user->getEmail());
        }
        $this->add([
            'type' => 'select',
            'name' => 'submitted_status',
            'options' => [
                'label' => 'Submitted status', // @translate
                'empty_option' => '',
                'value_options' => $valueOptions,
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select status', // @translate
            ],
        ]);
        $valueOptions = [
            'not_reviewed' => 'Not reviewed', // @translate
            'reviewed' => 'Reviewed', // @translate
        ];
        foreach ($this->getByUsers('reviewedBy', $this->getOption('project_id')) as $user) {
            $valueOptions[$user->getId()] = sprintf('Reviewed by %s (%s)', $user->getName(), $user->getEmail());
        }
        $this->add([
            'type' => 'select',
            'name' => 'reviewed_status',
            'options' => [
                'label' => 'Reviewed status', // @translate
                'empty_option' => '',
                'value_options' => $valueOptions,
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select status', // @translate
            ],
        ]);
        $valueOptions = [
            'not_locked' => 'Unlocked', // @translate
            'locked' => 'Locked', // @translate
        ];
        foreach ($this->getByUsers('lockedBy', $this->getOption('project_id')) as $user) {
            $valueOptions[$user->getId()] = sprintf('Locked by %s (%s)', $user->getName(), $user->getEmail());
        }
        $this->add([
            'type' => 'select',
            'name' => 'locked_status',
            'options' => [
                'label' => 'Locked status', // @translate
                'empty_option' => '',
                'value_options' => $valueOptions,
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select status', // @translate
            ],
        ]);
    }

    /**
     * Get users for the value options.
     *
     * This will only get users who are set in the $byColumn.
     *
     * @param string $byColumn
     * @param int $projectId
     * @param string $name
     * @param string $value
     * @param string $label
     * @return string
     */
    protected function getByUsers(string $byColumn, int $projectId)
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
        $query->setParameter('projectId', $projectId);
        $users = $query->getResult();
        usort($users, function ($userA, $userB) {
            return strcmp($userA->getName(), $userB->getName());
        });
        return $users;
    }
}
