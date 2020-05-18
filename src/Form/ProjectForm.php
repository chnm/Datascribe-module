<?php
namespace Datascribe\Form;

use Zend\Form\Form;

class ProjectForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => 'text',
            'name' => 'o-module-datascribe:name',
            'options' => [
                'label' => 'Name', // @translate
                'info' => 'Enter the name of this project.', // @translate
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
                'info' => 'Enter the description of this project.', // @translate
            ],
            'attributes' => [
                'required' => false,
                'id' => 'o-module-datascribe-description',
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
