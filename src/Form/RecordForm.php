<?php
namespace Datascribe\Form;

use Datascribe\Api\Representation\DatascribeDatasetRepresentation;
use Datascribe\DatascribeDataType\Manager;
use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Fieldset;

class RecordForm extends Form
{
    /**
     * @var Manager
     */
    protected $dataTypeManager;

    /**
     * @param Manager $dataTypeManager
     */
    public function setDataTypeManager(Manager $dataTypeManager)
    {
        $this->dataTypeManager = $dataTypeManager;
    }

    public function init()
    {
        $this->addCommonElements();
        $this->addValueElements();
    }

    /**
     * Add all elements that are common to all records.
     */
    protected function addCommonElements()
    {
        $item = $this->getOption('item');
        $record = $this->getOption('record');

        // Add "needs_review" element.
        if ($item->userIsAllowed('datascribe_flag_record_needs_review')) {
            $element = new Element\Checkbox('o-module-datascribe:needs_review');
            $element->setAttribute('required', false);
            $element->setValue($record ? $record->needsReview() : null);
            $this->add($element);
        }

        // Add "needs_work" element.
        if ($item->userIsAllowed('datascribe_flag_record_needs_work')) {
            $element = new Element\Checkbox('o-module-datascribe:needs_work');
            $element->setAttribute('required', false);
            $element->setValue($record ? $record->needsWork() : null);
            $this->add($element);
        }

        // Add "transcriber_notes" element.
        if ($item->userIsAllowed('datascribe_edit_transcriber_notes')) {
            $element = new Element\Textarea('o-module-datascribe:transcriber_notes');
            $element->setAttribute('required', false);
            $element->setValue($record ? $record->transcriberNotes() : null);
            $this->add($element);
        }

        // Add "reviewer_notes" element.
        if ($item->userIsAllowed('datascribe_edit_reviewer_notes')) {
            $element = new Element\Textarea('o-module-datascribe:reviewer_notes');
            $element->setAttribute('required', false);
            $element->setValue($record ? $record->reviewerNotes() : null);
            $this->add($element);
        }

        // Add "new_position" elements.
        if ($item->userIsAllowed('datascribe_change_record_position')) {
            $element = new DatascribeElement\OptionalSelect('new_position_direction');
            $element->setValueOptions([
                'before' => 'Insert before',
                'after' => 'Insert after',
            ]);
            $element->setEmptyOption('[Default position]');
            $this->add($element);

            $element = new Element\Select('new_position_reference');
            $valueOptions = [];
            foreach ($item->records() as $record) {
                $valueOptions[$record->position()] = $record->displayTitle();
            }
            $element->setValueOptions($valueOptions);
            $this->add($element);
        }
    }

    /**
     * Add all value elements configured for this dataset.
     */
    protected function addValueElements()
    {
        $item = $this->getOption('item');
        $record = $this->getOption('record');

        $valuesFieldset = new Fieldset('o-module-datascribe:value');
        $this->add($valuesFieldset);
        foreach ($item->dataset()->fields() as $field) {
            $valueFieldset = new Fieldset($field->id());
            $valueFieldset->setOption('datascribe_field', $field);
            $valuesFieldset->add($valueFieldset);
            $valueDataFieldset = new Fieldset('data');
            $valueFieldset->add($valueDataFieldset);

            $value = null;
            $valueText = null;
            $valueTextIsValid = true;
            if ($record) {
                $values = $record->values();
                if (isset($values[$field->id()])) {
                    $value = $values[$field->id()];
                    $valueFieldset->setOption('datascribe_value', $value);
                    $valueText = $value->text();
                    if (!$value->textIsValid()) {
                        $valueTextIsValid = false;
                    }
                }
            }

            // Add the custom value elements.
            $field->dataTypeService()->addValueElements(
                $valueDataFieldset,
                $field->data(),
                $valueTextIsValid ? $valueText : null
            );

            if (!$valueTextIsValid && (null !== $valueText)) {
                // Add a disabled textarea containing the invalid text.
                $element = new Element\Textarea('invalid_value_text');
                $element->setLabel('Invalid value'); // @translate
                $element->setAttributes([
                    'disabled' => true,
                    'rows' => 8,
                ]);
                $element->setValue($valueText);
                $valueFieldset->add($element);
            }

            // Add the common "set_null" element.
            $element = new Element\Checkbox('set_null');
            $element->setLabel('Set to null'); // @translate
            $element->setAttribute('required', false);
            $valueFieldset->add($element);

            // Add the common "is_missing" element.
            $element = new Element\Checkbox('is_missing');
            $element->setAttribute('required', false);
            $element->setValue($value ? $value->isMissing() : null);
            $valueFieldset->add($element);

            // Add the common "is_illegible" element.
            $element = new Element\Checkbox('is_illegible');
            $element->setAttribute('required', false);
            $element->setValue($value ? $value->isIllegible() : null);
            $valueFieldset->add($element);
        }
    }
}
