<?php
namespace Datascribe\Form;

use Zend\Form\Form;
use Omeka\Form\Element\ItemSetSelect;

class DatasetForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'o-module-datascribe:name',
            'type' => 'text',
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
            'name' => 'o-module-datascribe:description',
            'type' => 'textarea',
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
            'name' => 'o-module-datascribe:guidelines',
            'type' => 'textarea',
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
            'name' => 'o:item_set',
            'type' => ItemSetSelect::class,
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
}
