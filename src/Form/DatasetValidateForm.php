<?php
namespace Datascribe\Form;

use Laminas\Form\Form;

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
