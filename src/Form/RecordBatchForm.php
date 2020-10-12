<?php
namespace Datascribe\Form;

use Laminas\Form\Fieldset;

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
        $dataset = $this->getOption('dataset');

        $valuesFieldset = new Fieldset('values');
        $this->add($valuesFieldset);
        foreach ($dataset->fields() as $field) {
            $valueFieldset = new Fieldset($field->id());
            $valueFieldset->setLabel($field->name());
            $valuesFieldset->add($valueFieldset);
            $valueFieldset->add([
                'type' => 'select',
                'name' => 'is_missing_action',
                'options' => [
                    'label' => 'Is missing action', // @translate
                    'empty_option' => '',
                    'value_options' => [
                        '1' => 'Mark as missing', // @translate
                        '0' => 'Mark as not missing', // @translate
                    ],
                ],
                'attributes' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => '[No change]', // @translate
                ],
            ]);
            $valueFieldset->add([
                'type' => 'select',
                'name' => 'is_illegible_action',
                'options' => [
                    'label' => 'Is illegible action', // @translate
                    'empty_option' => '',
                    'value_options' => [
                        '1' => 'Mark as illegible', // @translate
                        '0' => 'Mark as not illegible', // @translate
                    ],
                ],
                'attributes' => [
                    'class' => 'chosen-select',
                    'data-placeholder' => '[No change]', // @translate
                ],
            ]);
            $valueFieldset->add([
                'type' => 'checkbox',
                'name' => 'edit_values',
                'options' => [
                    'label' => 'Edit values?', // @transcribe
                ],
            ]);
            $valueDataFieldset = new Fieldset('data');
            $valueFieldset->add($valueDataFieldset);
            $field->dataTypeService()->addValueElements(
                $valueDataFieldset,
                $field->data(),
                null
            );
        }

        $inputFilter = $this->getInputFilter();
        $values = $inputFilter->get('values');
        foreach ($dataset->fields() as $field) {
            $value = $values->get($field->id());
            $value->add([
                'name' => 'is_missing_action',
                'allow_empty' => true,
            ]);
            $value->add([
                'name' => 'is_illegible_action',
                'allow_empty' => true,
            ]);
        }
    }
}
