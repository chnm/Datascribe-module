<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Radio as ZendRadio;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class Radio extends ZendRadio implements InputProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');

        $valueOptions = array_combine($fieldData['options'], $fieldData['options']);
        $valueOptions[''] = '[No selection]'; // @translate
        $this->setValueOptions($valueOptions);

        $attributes = [];
        $this->setAttributes(array_filter($attributes));
    }

    public function getValidators()
    {
        $fieldData = $this->getOption('datascribe_field_data');

        $validators = [];

        $haystack = array_merge(['' => ''], $fieldData['options']);
        $validators[] = new Validator\InArray(['haystack' => $haystack]);

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
