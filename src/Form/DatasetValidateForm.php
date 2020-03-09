<?php
namespace Datascribe\Form;

use Zend\Form\Form;

class DatasetValidateForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Validate dataset', // @translate
            ],
        ]);
    }
}
