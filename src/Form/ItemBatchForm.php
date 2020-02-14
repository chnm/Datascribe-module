<?php
namespace Datascribe\Form;

use Datascribe\Entity\DatascribeUser;

class ItemBatchForm extends AbstractForm
{
    public function init()
    {
        $project = $this->getOption('project');

        $valueOptions = $this->getLockToOtherValueOptions([
            'unlock' => 'Unlock', // @translate
            'lock' => 'Lock to me', // @translate
        ]);
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
}
