<?php
namespace Datascribe\Form\Element;

use Laminas\Form\Element\Textarea as LaminasTextarea;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator;

class Textarea extends LaminasTextarea implements InputProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');

        $attributes = [];
        // Note that "pattern" is an invalid attribute for <textarea>. Rely on
        // server-side validation instead.
        $attributes['rows'] = $fieldData['rows'] ?? null;
        $attributes['minlength'] = $fieldData['minlength'] ?? null;
        $attributes['maxlength'] = $fieldData['maxlength'] ?? null;
        $attributes['placeholder'] = $fieldData['placeholder'] ?? null;

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
        if (isset($fieldData['minlength'])) {
            $validator = new Validator\StringLength(['min' => $fieldData['minlength']]);
            $validators[] = $validator;
        }
        if (isset($fieldData['maxlength'])) {
            $validator = new Validator\StringLength(['max' => $fieldData['maxlength']]);
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
