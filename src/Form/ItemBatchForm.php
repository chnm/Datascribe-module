<?php
namespace Datascribe\Form;

use Omeka\Form\Element\UserSelect;
use Zend\Form\Form;

class ItemBatchForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => UserSelect::class,
            'name' => 'locked_status',
            'options' => [
                'label' => 'Locked status', // @translate
                'info' => 'Select to unlock these items or to lock these items to a user.', // @translate
                'empty_option' => '',
                'prepend_value_options' => [
                    '0' => 'Unlock', // @translate
                ]
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => '[No change]', // @translate
            ],
        ]);
        $this->add([
            'type' => 'select',
            'name' => 'approved_status',
            'options' => [
                'label' => 'Approved status', // @translate
                'info' => 'Select to mark these items as approved, not approved, or not reviewed.', // @translate
                'empty_option' => '',
                'value_options' => [
                    '1' => 'Approved', // @translate
                    '0' => 'Not approved', // @translate
                    '2' => 'Not reviewed', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => '[No change]', // @translate
            ],
        ]);
        $this->add([
            'type' => 'select',
            'name' => 'prioritized_status',
            'options' => [
                'label' => 'Prioritized status', // @translate
                'info' => 'Select to mark these items as prioritized or not prioritized.', // @translate
                'empty_option' => '',
                'value_options' => [
                    '1' => 'Prioritized', // @translate
                    '0' => 'Not prioritized', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => '[No change]', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'locked_status',
            'allow_empty' => true,
        ]);
        $inputFilter->add([
            'name' => 'approved_status',
            'allow_empty' => true,
        ]);
        $inputFilter->add([
            'name' => 'prioritized_status',
            'allow_empty' => true,
        ]);
    }
}
