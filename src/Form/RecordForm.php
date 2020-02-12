<?php
namespace Datascribe\Form;

use Datascribe\Api\Representation\DatascribeDatasetRepresentation;
use Datascribe\DatascribeDataType\Manager;
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
        $record = $this->getOption('record');

        // Add "needs_review" element.
        $element = new Element\Checkbox('o-module-datascribe:needs_review');
        $element->setLabel('Needs review'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $record ? $record->needsReview() : null,
        ]);
        $this->add($element);

        // Add "needs_work" element.
        $element = new Element\Checkbox('o-module-datascribe:needs_work');
        $element->setLabel('Needs work'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $record ? $record->needsWork() : null,
        ]);
        $this->add($element);

        // Add "transcriber_notes" element.
        $element = new Element\Textarea('o-module-datascribe:transcriber_notes');
        $element->setLabel('Transcriber notes'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $record ? $record->transcriberNotes() : null,
        ]);
        $this->add($element);

        // Add "reviewer_notes" element.
        $element = new Element\Textarea('o-module-datascribe:reviewer_notes');
        $element->setLabel('Reviewer notes'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $record ? $record->reviewerNotes() : null,
        ]);
        $this->add($element);
    }

    /**
     * Add all value elements configured for this dataset.
     */
    protected function addValueElements()
    {
        $dataset = $this->getOption('dataset');
        $record = $this->getOption('record');

        $valuesFieldset = new Fieldset('o-module-datascribe:value');
        $this->add($valuesFieldset);
        foreach ($dataset->fields() as $field) {
            $dataType = $this->dataTypeManager->get($field->dataType());

            $valueFieldset = new Fieldset($field->id());
            $valueFieldset->setOption('datascribe_field_name', $field->name());
            $valueFieldset->setOption('datascribe_field_description', $field->description());
            $valuesFieldset->add($valueFieldset);
            $valueDataFieldset = new Fieldset('data');
            $valueFieldset->add($valueDataFieldset);

            $value = null;
            $valueData = [];
            if ($record) {
                $values = $record->values();
                if (isset($values[$field->id()])) {
                    $value = $values[$field->id()];
                    $valueData = $value->data();
                }
            }

            // Add the custom "data" elements.
            $dataType->addValueDataElements(
                $valueDataFieldset,
                $field->name(),
                $field->description(),
                $field->data(),
                $valueData
            );

            // Add the common "is_missing" element.
            $element = new Element\Checkbox('is_missing');
            $element->setLabel('Is missing'); // @translate
            $element->setAttributes([
                'required' => false,
                'value' => $value ? $value->isMissing() : null,
            ]);
            $valueFieldset->add($element);

            // Add the common "is_illegible" element.
            $element = new Element\Checkbox('is_illegible');
            $element->setLabel('Is illegible'); // @translate
            $element->setAttributes([
                'required' => false,
                'value' => $value ? $value->isIllegible() : null,
            ]);
            $valueFieldset->add($element);
        }
    }
}
