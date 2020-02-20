<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Number as ZendNumber;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class Number extends ZendNumber implements InputProviderInterface
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

        $validators = [];
        if (isset($fieldData['pattern'])) {
            $validator = new Validator\Regex(['pattern' => sprintf('/%s/', $fieldData['pattern'])]);
            $validators[] = $validator;
        }
        if (isset($fieldData['min'])) {
            $validator = new Validator\GreaterThan(['min' => $fieldData['min'], 'inclusive' => true]);
            $validators[] = $validator;
        }
        if (isset($fieldData['max'])) {
            $validator = new Validator\LessThan(['max' => $fieldData['max'], 'inclusive' => true]);
            $validators[] = $validator;
        }
        if (isset($fieldData['step'])) {
            $validator = new Validator\Step(['step' => $fieldData['step']]);
            $validators[] = $validator;
        }
        return $validators;
    }

    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => null,
            'validators' => $this->getValidators(),
            'filters' => [],
        ];
    }
}
