<?php
namespace Datascribe\Form\Element;

use Zend\Form\Element\Select as ZendSelect;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator;

class MinuteSelect extends ZendSelect implements InputProviderInterface
{
    const MINUTES = [
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
        24 => '24',
        25 => '25',
        26 => '26',
        27 => '27',
        28 => '28',
        29 => '29',
        30 => '30',
        31 => '31',
        32 => '32',
        33 => '33',
        34 => '34',
        35 => '35',
        36 => '36',
        37 => '37',
        38 => '38',
        39 => '39',
        40 => '40',
        41 => '41',
        42 => '42',
        43 => '43',
        44 => '44',
        45 => '45',
        46 => '46',
        47 => '47',
        48 => '48',
        49 => '49',
        50 => '50',
        51 => '51',
        52 => '52',
        53 => '53',
        54 => '54',
        55 => '55',
        56 => '56',
        57 => '57',
        58 => '58',
        59 => '59',
    ];

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $fieldData = $this->getOption('datascribe_field_data');

        $this->setValueOptions(self::MINUTES);
        $this->setEmptyOption('');
    }

    public function getValidators()
    {
        $fieldData = $this->getOption('datascribe_field_data');

        $validators = [];
        $validators[] = new Validator\Between(['min' => 1, 'max' => 59, 'inclusive' => true]);
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
