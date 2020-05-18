<?php
namespace Datascribe\Form;

use Zend\Form\Form;

class DatasetExportForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Export dataset', // @translate
            ],
        ]);
    }
}
