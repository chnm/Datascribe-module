<?php
namespace Datascribe\Form;

use Datascribe\Entity\DatascribeUser;
use Doctrine\ORM\EntityManager;
use Omeka\Form\Element\UserSelect;
use Zend\Form\Form;

class ItemBatchForm extends Form
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
        $valueOptions = [
            'unlock' => 'Unlock',
            'lock' => 'Lock to me',
            'transcribers' => [
                'label' => 'Lock to transcriber', // @translate
                'options' => [],
            ],
            'reviewers' => [
                'label' => 'Lock to reviewer', // @translate
                'options' => [],
            ],
            'admins' => [
                'label' => 'Lock to admin', // @translate
                'options' => [],
            ],
        ];
        foreach ($this->getAdminUsers() as $user) {
            $valueOptions['admins']['options'][$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
        }
        foreach ($this->getProjectUsers() as $user) {
            $oUser = $user->getUser();
            if (DatascribeUser::ROLE_REVIEWER === $user->getRole()) {
                $valueOptions['reviewers']['options'][$user->getId()] = sprintf('%s (%s)', $oUser->getName(), $oUser->getEmail());
            } elseif (DatascribeUser::ROLE_TRANSCRIBER === $user->getRole()) {
                $valueOptions['transcribers']['options'][$user->getId()] = sprintf('%s (%s)', $oUser->getName(), $oUser->getEmail());
            }
        }
        $this->add([
            'type' => 'select',
            'name' => 'lock_action',
            'options' => [
                'label' => 'Lock action', // @translate
                'empty_option' => '',
                'value_options' => $valueOptions,
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => '[No change]', // @translate
            ],
        ]);
        $this->add([
            'type' => 'select',
            'name' => 'review_action',
            'options' => [
                'label' => 'Review action', // @translate
                'empty_option' => '',
                'value_options' => [
                    'approved' => 'Mark as approved', // @translate
                    'not_approved' => 'Mark as not approved', // @translate
                    'not_reviewed' => 'Mark as not reviewed', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => '[No change]', // @translate
            ],
        ]);
        $this->add([
            'type' => 'select',
            'name' => 'priority_action',
            'options' => [
                'label' => 'Priority action', // @translate
                'empty_option' => '',
                'value_options' => [
                    'prioritized' => 'Mark as prioritized', // @translate
                    'not_prioritized' => 'Mark as not prioritized', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => '[No change]', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'lock_action',
            'allow_empty' => true,
        ]);
        $inputFilter->add([
            'name' => 'review_action',
            'allow_empty' => true,
        ]);
        $inputFilter->add([
            'name' => 'priority_action',
            'allow_empty' => true,
        ]);
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
        $projectId = $this->getOption('project_id');
        if (!$projectId) {
            return [];
        }
        $dql = "
        SELECT u
        FROM Datascribe\Entity\DatascribeUser u
        JOIN u.project p
        JOIN u.user ou
        WHERE p = :projectId
        ORDER BY ou.name";
        $query = $this->em->createQuery($dql);
        $query->setParameter('projectId', $projectId);
        return $query->getResult();
    }
}
