<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Select as ZendSelect;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class HourSelect extends ZendSelect implements InputProviderInterface
{
    const HOURS = [
        0 => '00',
        1 => '01',
        2 => '02',
        3 => '03',
        4 => '04',
        5 => '05',
        6 => '06',
        7 => '07',
        8 => '08',
        9 => '09',
        10 => '10',
        11 => '11',
        12 => '12',
        13 => '13',
        14 => '14',
        15 => '15',
        16 => '16',
        17 => '17',
        18 => '18',
        19 => '19',
        20 => '20',
        21 => '21',
        22 => '22',
        23 => '23',
    ];

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');

        $this->setValueOptions(self::HOURS);
        $this->setEmptyOption('');
    }

    public function getValidators()
    {
        $fieldData = $this->getOption('datascribe_field_data');

        $validators = [];
        $validators[] = new Validator\Between(['min' => 0, 'max' => 23, 'inclusive' => true]);
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
