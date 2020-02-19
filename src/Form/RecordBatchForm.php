<?php
namespace Datascribe\Form;

use Datascribe\Entity\DatascribeUser;

class RecordBatchForm extends AbstractForm
{
    public function init()
    {
        $this->add([
            'type' => 'select',
            'name' => 'needs_review_action',
            'options' => [
                'label' => 'Needs review action', // @translate
                'empty_option' => '',
                'value_options' => [
                    '1' => 'Mark as needs review', // @translate
                    '0' => 'Mark as does not need review', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => '[No change]', // @translate
            ],
        ]);
        $this->add([
            'type' => 'select',
            'name' => 'needs_work_action',
            'options' => [
                'label' => 'Needs work action', // @translate
                'empty_option' => '',
                'value_options' => [
                    '1' => 'Mark as needs work', // @translate
                    '0' => 'Mark as does not need work', // @translate
                ],
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => '[No change]', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'needs_review_action',
            'allow_empty' => true,
        ]);
        $inputFilter->add([
            'name' => 'needs_work_action',
            'allow_empty' => true,
        ]);
    }
}
