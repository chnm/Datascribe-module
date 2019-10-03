<?php
namespace Datascribe\Form;

use Zend\Form\Form;
use Omeka\Form\Element\ItemSetSelect;
use Omeka\Form\Element\PropertySelect;

class ProjectForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'o-module-datascribe:name',
            'type' => 'text',
            'options' => [
                'label' => 'Name', // @translate
                'info' => 'Enter the name of this project.', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
        $this->add([
            'name' => 'o-module-datascribe:description',
            'type' => 'textarea',
            'options' => [
                'label' => 'Description', // @translate
                'info' => 'Enter the description of this project.', // @translate
            ],
            'attributes' => [
                'required' => false,
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
    }
}
