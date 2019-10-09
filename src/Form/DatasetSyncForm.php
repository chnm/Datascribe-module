<?php
namespace Datascribe\Form;

use Zend\Form\Form;

class DatasetSyncForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Sync dataset', // @translate
            ],
        ]);

        // Disable the submit button if the dataset has no item set.
        $dataset = $this->getOption('dataset');
        if ($dataset && !$dataset->itemSet()) {
            $this->get('submit')->setAttribute('disabled', true);
        }
    }
}
