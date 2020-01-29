<?php
namespace Datascribe\Form;

use Datascribe\DatascribeDataType\DataTypeInterface;
use Datascribe\Entity\DatascribeField;
use Datascribe\Form\Element as DatascribeElement;
use Omeka\Form\Element\ItemSetSelect;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Fieldset;

class DatasetForm extends Form
{
    public function init()
    {
        $this->addCommonElements();
        if ($this->getOption('dataset')) {
            $this->addFieldsElements();
        }

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'o-module-datascribe:name',
            'required' => true,
            'filters' => [
                ['name' => 'toNull'],
            ],
        ]);
        $inputFilter->add([
            'name' => 'o-module-datascribe:description',
            'required' => false,
            'filters' => [
                ['name' => 'toNull'],
            ],
        ]);
        $inputFilter->add([
            'name' => 'o-module-datascribe:guidelines',
            'required' => false,
            'filters' => [
                ['name' => 'toNull'],
            ],
        ]);
    }

    protected function addCommonElements()
    {
        $this->add([
            'type' => 'text',
            'name' => 'o-module-datascribe:name',
            'options' => [
                'label' => 'Name', // @translate
                'info' => 'Enter the name of this dataset.', // @translate
            ],
            'attributes' => [
                'required' => true,
                'id' => 'o-module-datascribe-name',
            ],
        ]);
        $this->add([
            'type' => 'textarea',
            'name' => 'o-module-datascribe:description',
            'options' => [
                'label' => 'Description', // @translate
                'info' => 'Enter the description of this dataset.', // @translate
            ],
            'attributes' => [
                'required' => false,
                'id' => 'o-module-datascribe-description',
            ],
        ]);
        $this->add([
            'type' => 'textarea',
            'name' => 'o-module-datascribe:guidelines',
            'options' => [
                'label' => 'Guidelines', // @translate
                'info' => 'Enter guidelines for transcribing this dataset.', // @translate
            ],
            'attributes' => [
                'required' => false,
                'id' => 'o-module-datascribe-guidelines',
            ],
        ]);
        $this->add([
            'type' => ItemSetSelect::class,
            'name' => 'o:item_set',
            'options' => [
                'label' => 'Item set', // @translate
                'info' => 'Select the item set used to synchronize dataset items. Once synchronized, this dataset will contain every item in this item set.', // @translate
                'empty_option' => '',
                'show_required' => true,
            ],
            'attributes' => [
                'class' => 'chosen-select',
                'data-placeholder' => 'Select an item set', // @translate
                'id' => 'o-item-set',
            ],
        ]);
    }

    /**
     * Add all elements for all fields.
     */
    public function addFieldsElements()
    {
        $manager = $this->getOption('data_type_manager');
        $dataset = $this->getOption('dataset');

        $fieldsFieldset = new Fieldset('o-module-datascribe:field');
        $this->add($fieldsFieldset);
        foreach ($dataset->fields() as $field) {
            $dataType = $manager->get($field->getDataType());

            $fieldFieldset = new Fieldset($field->getId());
            $fieldsFieldset->add($fieldFieldset);
            $fieldFieldset->setLabel(sprintf(
                '<span class="field-name">%s</span><span class="data-type-label">%s</span>',
                $field->getName(),
                $dataType->getLabel()
            ));
            $fieldFieldset->setLabelOptions(['disable_html_escape' => true]);
            $fieldFieldset->setAttribute('class', $field->getDataType());

            $element = new Element\Hidden('o:id');
            $element->setAttribute('value', $field->getId());
            $fieldFieldset->add($element);

            $this->addFieldElements($fieldFieldset, $dataType, $field);
        }
    }

    /**
     * Add all elements for a field.
     *
     * @param Fieldset $fieldFieldset
     * @param DataTypeInterface $dataType
     * @param ?DatascribeField $field
     */
    public function addFieldElements(Fieldset $fieldFieldset, DataTypeInterface $dataType, ?DatascribeField $field)
    {
        // Add the common "name" element.
        $element = new Element\Text('o-module-datascribe:name');
        $element->setLabel('Field name'); // @translate
        $element->setAttributes([
            'required' => true,
            'value' => $field ? $field->getName() : null,
        ]);
        $fieldFieldset->add($element);

        // Add the common "description" element.
        $element = new Element\Text('o-module-datascribe:description');
        $element->setLabel('Field description'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $field ? $field->getDescription() : null,
        ]);
        $fieldFieldset->add($element);

        // Add the common "is_primary" element.
        $element = new DatascribeElement\OptionalCheckbox('o-module-datascribe:is_primary');
        $element->setLabel('Field is primary'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $field ? $field->getIsPrimary() : null,
        ]);
        $fieldFieldset->add($element);

        // Add the custom "data" elements.
        $fieldDataFieldset = new Fieldset('data');
        $fieldFieldset->add($fieldDataFieldset);
        $dataType->addFieldDataElements($fieldDataFieldset, $field ? $field->getData() : []);
    }
}
