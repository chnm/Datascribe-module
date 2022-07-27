<?php
namespace Datascribe\Form;

use Laminas\Form\Form;
use Omeka\Form\Element\ResourceSelect;

class DatasetMoveForm extends Form
{
    public function init()
    {
        $dataset = $this->getOption('dataset');
        $this->add([
            'type' => ResourceSelect::class,
            'name' => 'project_id',
            'options' => [
                'label' => 'Project', // @translate
                'resource_value_options' => [
                    'resource' => 'datascribe_projects',
                    'option_text_callback' => function($project) {
                        return $project->name();
                    },
                ],
            ],
            'attributes' => [
                'value' => $dataset->project()->id(),
                'required' => true,
            ],
        ]);
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Move dataset', // @translate
            ],
        ]);
    }
}
