<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Select as ZendSelect;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class MonthSelect extends ZendSelect implements InputProviderInterface
{
    const MONTHS = [
        1 => 'January', // @translate
        2 => 'February', // @translate
        3 => 'March', // @translate
        4 => 'April', // @translate
        5 => 'May', // @translate
        6 => 'June', // @translate
        7 => 'July', // @translate
        8 => 'August', // @translate
        9 => 'September', // @translate
        10 => 'October', // @translate
        11 => 'November', // @translate
        12 => 'December', // @translate
    ];

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');

        $this->setValueOptions(self::MONTHS);
        $this->setEmptyOption('');
    }

    public function getValidators()
    {
        $fieldData = $this->getOption('datascribe_field_data');

        $validators = [];
        $validators[] = new Validator\Between(['min' => 1, 'max' => 12, 'inclusive' => true]);
        $validators[] = new Validator\Regex(['pattern' => '/\d+/']);

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
