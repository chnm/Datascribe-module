<?php
namespace Datascribe\Form\Element;

use Laminas\Form\Element\Number as LaminasNumber;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator;

class Number extends LaminasNumber implements InputProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');

        $attributes = [];
        $attributes['min'] = $fieldData['min'] ?? null;
        $attributes['max'] = $fieldData['max'] ?? null;
        $attributes['step'] = $fieldData['step'] ?? 'any';
        $attributes['placeholder'] = $fieldData['placeholder'] ?? null;
        $attributes['style'] = 'width: 100%;';

        $this->setAttributes(array_filter($attributes));
    }

    public function getValidators()
    {
        $fieldData = $this->getOption('datascribe_field_data');

        $validators = parent::getValidators();
        if (isset($fieldData['pattern'])) {
            $validator = new Validator\Regex(['pattern' => sprintf('/%s/', $fieldData['pattern'])]);
            $validators[] = $validator;
        }
        return $validators;
    }

    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => false,
            'allow_empty' => true,
            'validators' => $this->getValidators(),
            'filters' => [],
        ];
    }
}
