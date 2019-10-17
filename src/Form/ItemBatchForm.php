<?php
namespace Datascribe\Form;

use Zend\Form\Form;

class ItemBatchForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => 'radio',
            'name' => 'locked',
            'options' => [
                'label' => 'Lock  status', // @translate
                'value_options' => [
                    '1' => 'Locked', // @translate
                    '0' => 'Unlocked', // @translate
                    '' => '[No change]', // @translate
                ],
            ],
            'attributes' => [
                'value' => '',
            ],
        ]);
        $this->add([
            'type' => 'Omeka\Form\Element\UserSelect',
            'name' => 'locked_user',
            'options' => [
                'label' => 'Lock to user', // @translate
                'empty_option' => '',
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select a user', // @translate
            ],
        ]);
        $this->add([
            'name' => 'prioritized',
            'type' => 'radio',
            'options' => [
                'label' => 'Priority status', // @translate
                'value_options' => [
                    '1' => 'Prioritized', // @translate
                    '0' => 'Not prioritized', // @translate
                    '' => '[No change]', // @translate
                ],
            ],
            'attributes' => [
                'value' => '',
            ],
        ]);
        $this->add([
            'name' => 'reviewed',
            'type' => 'radio',
            'options' => [
                'label' => 'Review status', // @translate
                'value_options' => [
                    '1' => 'Approved', // @translate
                    '0' => 'Not approved', // @translate
                    '' => '[No change]', // @translate
                ],
            ],
            'attributes' => [
                'value' => '',
            ],
        ]);

        $inputFilter = $this->getInputFilter();
    }
}
