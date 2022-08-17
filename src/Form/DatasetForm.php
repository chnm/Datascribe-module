<?php
namespace Datascribe\Form;

use Datascribe\Api\Representation\DatascribeFieldRepresentation;
use Datascribe\DatascribeDataType\DataTypeInterface;
use Datascribe\DatascribeDataType\Manager;
use Datascribe\DatascribeDataType\Unknown;
use Datascribe\Form\Element as DatascribeElement;
use Omeka\Form\Element\ItemSetSelect;
use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Form\Fieldset;
use Laminas\View\HelperPluginManager;

class DatasetForm extends Form
{
    /**
     * @var Manager
     */
    protected $dataTypeManager;

    /**
     * @var HelperPluginManager
     */
    protected $viewHelperManager;

    /**
     * @param Manager $dataTypeManager
     */
    public function setDataTypeManager(Manager $dataTypeManager)
    {
        $this->dataTypeManager = $dataTypeManager;
    }

    /**
     * @param HelperPluginManager $viewHelperManager
     */
    public function setViewHelperManager(HelperPluginManager $viewHelperManager)
    {
        $this->viewHelperManager = $viewHelperManager;
    }

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
        $inputFilter->add([
            'name' => 'o-module-datascribe:approved_item_behavior',
            'required' => false,
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
        $this->add([
            'type' => 'checkbox',
            'name' => 'o-module-datascribe:revert_review_status',
            'options' => [
                'label' => 'Revert review status', // @translate
                'info' => 'Check this to automatically revert an approved item\'s review status to "not reviewed" when a record belonging to the item is added or modified. The default behavior is to retain the approved review status.', // @translate
            ],
            'attributes' => [
                'id' => 'o-module-datascribe-revert-review-status'
            ],
        ]);
        $this->add([
            'type' => 'checkbox',
            'name' => 'o-module-datascribe:export_missing_illegible',
            'options' => [
                'label' => 'Export missing and illegible', // @translate
                'info' => 'Check this to flag values as missing and/or illegible in the exported dataset. This will add two columns to the right of every value: is_missing and is_illegible. The default behavior is to omit the is_missing and is_illegible columns.', // @translate
            ],
            'attributes' => [
                'id' => 'o-module-datascribe-export-missing-illegible'
            ],
        ]);
        $this->add([
            'type' => 'file',
            'name' => 'import_form',
            'options' => [
                'label' => 'Import form', // @translate
                'info' => 'You may import fields that were exported from another form.', // @translate
            ],
            'attributes' => [
                'id' => 'import-form',
                'accept' => '.json,application/json',
            ],
        ]);
    }

    /**
     * Get all datasets keyed by the data set name.
     *
     * @return array
     */
    public function dataTypes() : array
    {
        $dataTypes = [];
        $dataTypeNames = $this->dataTypeManager->getRegisteredNames();
        natcasesort($dataTypeNames);
        foreach ($dataTypeNames as $dataTypeName) {
            $dataType = $this->dataTypeManager->get($dataTypeName);
            if (!($dataType instanceof Unknown)) {
                $dataTypes[$dataTypeName] = $dataType;
            }
        }
        return $dataTypes;
    }

    /**
     * Get field templates for every data type.
     */
    public function dataTypeTemplates() : string
    {
        $escapeHtml = $this->viewHelperManager->get('escapeHtml');
        $translate = $this->viewHelperManager->get('translate');
        $formCollection = $this->viewHelperManager->get('formCollection');

        $templates = [];
        foreach ($this->dataTypes() as $dataTypeName => $dataType) {
            $fieldFieldset = new Fieldset('__INDEX__');
            $fieldFieldset->setLabel(sprintf(
                '<span class="field-name">%s</span><span class="data-type-label">%s</span>',
                $translate('New field'),
                $translate($dataType->getLabel())
            ));
            $fieldFieldset->setLabelOptions(['disable_html_escape' => true]);
            $fieldFieldset->setAttribute('class', sprintf('dataset-field %s', $dataTypeName));

            $element = new Element\Hidden('data_type');
            $element->setAttribute('value', $dataTypeName);
            $fieldFieldset->add($element);

            $this->addFieldElements($fieldFieldset, $dataType, null);

            // Mock the form so prepare() can build the name attributes.
            $mockForm = new Form;
            $mockForm->add(new Fieldset('o-module-datascribe:field'));
            $mockForm->get('o-module-datascribe:field')->add($fieldFieldset);
            $mockForm->prepare();

            $templates[] = sprintf(
                '<span class="data-type-template" data-name="%s" data-template="%s"></span>',
                $escapeHtml($dataTypeName),
                $escapeHtml($formCollection($fieldFieldset))
            );
        }

        return implode("\n", $templates);
    }

    /**
     * Add all elements for all fields.
     */
    public function addFieldsElements()
    {
        $dataset = $this->getOption('dataset');
        $translate = $this->viewHelperManager->get('translate');

        $fieldsFieldset = new Fieldset('o-module-datascribe:field');
        $fieldsFieldset->setAttribute('class', 'dataset-fields');
        $this->add($fieldsFieldset);
        foreach ($dataset->fields() as $field) {
            $dataType = $field->dataTypeService();

            $fieldFieldset = new Fieldset($field->id());
            $fieldsFieldset->add($fieldFieldset);
            $fieldFieldset->setLabel(sprintf(
                '<span class="field-name">%s</span><span class="data-type-label">%s</span>',
                $field->name(),
                $translate($dataType->getLabel())
            ));
            $fieldFieldset->setLabelOptions(['disable_html_escape' => true]);
            $fieldFieldset->setAttribute('class', sprintf('dataset-field %s', $field->dataType()));

            $this->addFieldElements($fieldFieldset, $dataType, $field);
        }
    }

    /**
     * Add all elements for a field.
     *
     * @param Fieldset $fieldFieldset
     * @param DataTypeInterface $dataType
     * @param ?DatascribeFieldRepresentation $field
     */
    public function addFieldElements(Fieldset $fieldFieldset, DataTypeInterface $dataType, ?DatascribeFieldRepresentation $field)
    {
        // Add the common "name" element.
        $element = new DatascribeElement\RequiredText('name');
        $element->setLabel('Field name'); // @translate
        $element->setAttributes([
            'required' => true,
            'value' => $field ? $field->name() : null,
        ]);
        $fieldFieldset->add($element);

        // Add the common "description" element.
        $element = new Element\Text('description');
        $element->setLabel('Field description'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $field ? $field->description() : null,
        ]);
        $fieldFieldset->add($element);

        // Add the common "is_primary" element.
        $element = new DatascribeElement\OptionalCheckbox('is_primary');
        $element->setLabel('Field is primary'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $field ? $field->isPrimary() : null,
        ]);
        $fieldFieldset->add($element);

        // Add the common "is_required" element.
        $element = new DatascribeElement\OptionalCheckbox('is_required');
        $element->setLabel('Field is required'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $field ? $field->isRequired() : null,
        ]);
        $fieldFieldset->add($element);

        // Add the custom "data" elements.
        $fieldDataFieldset = new Fieldset('data');
        $fieldDataFieldset->setLabel('Options'); // @translate
        $fieldDataFieldset->setAttribute('class', 'dataset-field-data');
        $fieldFieldset->add($fieldDataFieldset);
        $dataType->addFieldElements($fieldDataFieldset, $field ? $field->data() : []);
        if (0 === $fieldDataFieldset->count()) {
            // Remove the fieldset if the data type adds no field elements.
            $fieldFieldset->remove('data');
        }
    }

    /**
     * Remove deleted fields.
     *
     * We must explicitly remove deleted fields from the form or it will not
     * validate if any field elements are required (note that field names are
     * always required).
     *
     * @param array $postData
     * @return self
     */
    public function removeDeletedFields(array $postData)
    {
        // Fields deleted by the form builder are not passed with POST data.
        $fieldIdsToRetain = array_keys($postData['o-module-datascribe:field']);
        $fieldsFieldset = $this->get('o-module-datascribe:field');
        $fieldsInputFilter = $this->getInputFilter()->get('o-module-datascribe:field');
        foreach ($fieldsFieldset->getFieldsets() as $fieldId => $fieldFieldset) {
            if (!in_array($fieldId, $fieldIdsToRetain)) {
                // This field was deleted by the form builder. Delete it from
                // the form by removing the field's fieldset and input filter.
                $fieldsFieldset->remove($fieldId);
                $fieldsInputFilter->remove($fieldId);
            }
        }
        return $this;
    }
}
