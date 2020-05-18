<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Select as ZendSelect;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class YearSelect extends ZendSelect implements InputProviderInterface
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');

        $currentYear = (int) date('Y');
        $yearMin = $fieldData['min_year'] ?? $currentYear - 100;
        $yearMax = $fieldData['max_year'] ?? $currentYear;
        $years = range($yearMin, $yearMax);
        $this->setValueOptions(array_combine($years, $years));
        $this->setEmptyOption('');
    }

    public function getValidators()
    {
        $fieldData = $this->getOption('datascribe_field_data');

        $validators = [];
        if (isset($fieldData['min_year'])) {
            $validator = new Validator\GreaterThan(['min' => $fieldData['min_year'], 'inclusive' => true]);
            $validators[] = $validator;
        }
        if (isset($fieldData['max_year'])) {
            $validator = new Validator\LessThan(['max' => $fieldData['max_year'], 'inclusive' => true]);
            $validators[] = $validator;
        }
        $validators[] = new Validator\Regex(['pattern' => '/-?\d+/']);

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
