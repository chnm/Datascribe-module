<?php
namespace Datascribe\Form;

class RecordSearchForm extends AbstractForm
{
    public function init()
    {
        $item = $this->getOption('item');

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
        foreach ($this->getByUsersForRecords('createdBy', $item) as $user) {
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
        foreach ($this->getByUsersForRecords('modifiedBy', $item) as $user) {
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
    }
}
