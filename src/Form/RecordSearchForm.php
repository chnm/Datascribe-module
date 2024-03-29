<?php
namespace Datascribe\Form;

class RecordSearchForm extends AbstractForm
{
    public function init()
    {
        $parent = $this->getOption('parent');

        $this->add([
            'type' => 'select',
            'name' => 'needs_review',
            'options' => [
                'label' => 'Needs review?', // @translate
                'empty_option' => '',
                'value_options' => [
                    '1' => 'Yes', // @translate
                    '0' => 'No', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select one', // @translate
            ],
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'needs_work',
            'options' => [
                'label' => 'Needs work?', // @translate
                'empty_option' => '',
                'value_options' => [
                    '1' => 'Yes', // @translate
                    '0' => 'No', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select one', // @translate
            ],
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'has_invalid_values',
            'options' => [
                'label' => 'Has invalid values?', // @translate
                'empty_option' => '',
                'value_options' => [
                    '1' => 'Yes', // @translate
                    '0' => 'No', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select one', // @translate
            ],
        ]);

        $valueOptions = [];
        foreach ($this->getByUsersForRecords('createdBy', $parent) as $user) {
            $valueOptions[$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
        }
        $this->add([
            'type' => 'select',
            'name' => 'created_by',
            'options' => [
                'label' => 'Created by', // @translate
                'empty_option' => '',
                'value_options' => $valueOptions,
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select user', // @translate
            ],
        ]);

        $valueOptions = [];
        foreach ($this->getByUsersForRecords('modifiedBy', $parent) as $user) {
            $valueOptions[$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
        }
        $this->add([
            'type' => 'select',
            'name' => 'modified_by',
            'options' => [
                'label' => 'Modified by', // @translate
                'empty_option' => '',
                'value_options' => $valueOptions,
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select user', // @translate
            ],
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'transcriber_notes_status',
            'options' => [
                'label' => 'Transcriber notes status', // @translate
                'empty_option' => '',
                'value_options' => [
                    'is_not_null' => 'Has notes', // @translate
                    'is_null' => 'Has no notes', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select status', // @translate
            ],
        ]);
        $this->add([
            'type' => 'select',
            'name' => 'reviewer_notes_status',
            'options' => [
                'label' => 'Reviewer notes status', // @translate
                'empty_option' => '',
                'value_options' => [
                    'is_not_null' => 'Has notes', // @translate
                    'is_null' => 'Has no notes', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select status', // @translate
            ],
        ]);
        $this->add([
            'type' => 'text',
            'name' => 'transcriber_notes',
            'options' => [
                'label' => 'Search transcriber notes', // @translate
            ],
        ]);
        $this->add([
            'type' => 'text',
            'name' => 'reviewer_notes',
            'options' => [
                'label' => 'Search reviewer notes', // @translate
            ],
        ]);
    }
}
