<?php
namespace Datascribe\Form\Element;

use Laminas\Form\Element\Select as LaminasSelect;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator;

class DaySelect extends LaminasSelect implements InputProviderInterface
{
    const DAYS = [
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
        24 => '24',
        25 => '25',
        26 => '26',
        27 => '27',
        28 => '28',
        29 => '29',
        30 => '30',
        31 => '31',
    ];

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');

        $this->setValueOptions(self::DAYS);
        $this->setEmptyOption('');
    }

    public function getValidators()
    {
        $fieldData = $this->getOption('datascribe_field_data');

        $validators = [];
        $validators[] = new Validator\Between(['min' => 1, 'max' => 31, 'inclusive' => true]);
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
