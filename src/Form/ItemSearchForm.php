<?php
namespace Datascribe\Form;

class ItemSearchForm extends AbstractForm
{
    public function init()
    {
        $dataset = $this->getOption('dataset');

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
        foreach ($this->getByUsersForItems('submittedBy', $dataset) as $user) {
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
        foreach ($this->getByUsersForItems('reviewedBy', $dataset) as $user) {
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
        foreach ($this->getByUsersForItems('lockedBy', $dataset) as $user) {
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
}
