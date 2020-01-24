<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Textarea as ZendTextarea;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class Textarea extends ZendTextarea implements InputProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');
        $valueData = $this->getOption('datascribe_value_data');

        $attributes = [];
        // Note that "pattern" is an invalid attribute for <textarea>. Rely on
        // server-side validation instead.
        $attributes['rows'] = $fieldData['rows'] ?? null;
        $attributes['minlength'] = $fieldData['minlength'] ?? null;
        $attributes['maxlength'] = $fieldData['maxlength'] ?? null;
        $attributes['placeholder'] = $fieldData['placeholder'] ?? null;

        $value = null;
        if (isset($valueData['text'])) {
            $value = $valueData['text'];
        } elseif (isset($fieldData['default_value'])) {
            $value = $fieldData['default_value'];
        }

        $this->setAttributes(array_filter($attributes));
        $this->setValue($value);
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
            'name' => 'text',
            'required' => null,
            'validators' => $this->getValidators(),
            'filters' => [],
        ];
    }
}
