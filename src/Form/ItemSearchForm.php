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
            'submitted_by' => [
                'label' => 'Submitted by', // @translate
                'options' => [],
            ],
        ];
        foreach ($this->getByUsers('submittedBy') as $user) {
            $valueOptions['submitted_by']['options'][$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
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
            'reviewed_by' => [
                'label' => 'Reviewed by', // @translate
                'options' => [],
            ],
        ];
        foreach ($this->getByUsers('reviewedBy') as $user) {
            $valueOptions['reviewed_by']['options'][$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
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
            'locked_by' => [
                'label' => 'Locked by', // @translate
                'options' => [],
            ],
        ];
        foreach ($this->getByUsers('lockedBy') as $user) {
            $valueOptions['locked_by']['options'][$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
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
     * Get project users for the value options.
     *
     * This will only get users who are set in the $byColumn.
     *
     * @param string $byColumn
     * @return string
     */
    protected function getByUsers(string $byColumn)
    {
        if (!in_array($byColumn, ['lockedBy', 'submittedBy', 'reviewedBy'])) {
            return [];
        }
        $projectId = $this->getOption('project_id');
        if (!$projectId) {
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
