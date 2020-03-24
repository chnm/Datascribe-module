<?php
namespace Datascribe\Form;

use Datascribe\Entity\DatascribeUser;
use Zend\Form\Element;
use Zend\Form\Fieldset;

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

        $this->addValueElements();

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

    /**
     * Add all value elements configured for this dataset.
     */
    protected function addValueElements()
    {
        $item = $this->getOption('item');

        $valuesFieldset = new Fieldset('values');
        $this->add($valuesFieldset);
        foreach ($item->dataset()->fields() as $field) {
            $valueFieldset = new Fieldset($field->id());
            $valueFieldset->setLabel($field->name());
            $valuesFieldset->add($valueFieldset);
            $valueDataFieldset = new Fieldset('data');
            $valueFieldset->add($valueDataFieldset);

            // Add the custom value elements.
            $field->dataTypeService()->addValueElements($valueDataFieldset, $field->data(), null);

            // Add the common "is_missing" element.
            $valueFieldset->add([
                'type' => 'select',
                'name' => 'is_missing_action',
                'options' => [
                    'label' => 'Is missing action', // @translate
                    'empty_option' => '',
                    'value_options' => [
                        'is_missing' => 'Mark as missing', // @translate
                        'not_is_missing' => 'Mark as not missing', // @translate
                    ],
                ],
                'attributes' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => '[No change]', // @translate
                ],
            ]);

            // Add the common "is_illegible" element.
            $valueFieldset->add([
                'type' => 'select',
                'name' => 'is_illegible_action',
                'options' => [
                    'label' => 'Is illegible action', // @translate
                    'empty_option' => '',
                    'value_options' => [
                        'is_illegible' => 'Mark as illegible', // @translate
                        'not_is_illegible' => 'Mark as not illegible', // @translate
                    ],
                ],
                'attributes' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => '[No change]', // @translate
                ],
            ]);
        }
    }
}
