<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Validator\ValidatorChain;

class Datetime implements DataTypeInterface
{
    public function getLabel() : string
    {
        return 'DateTime'; // @translate
    }

    public function addFieldDataElements(Fieldset $fieldset, array $fieldData) : void
    {
        $element = new DatascribeElement\OptionalNumber('min_year');
        $element->setLabel('Minimum year'); // @translate
        $element->setValue($fieldData['min_year'] ?? null);
        $element->setAttributes([
        ]);
        $element->setAttributes([
            'step' => 1,
        ]);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalNumber('max_year');
        $element->setLabel('Maximum year'); // @translate
        $element->setValue($fieldData['max_year'] ?? null);
        $element->setAttributes([
            'step' => 1,
        ]);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalNumber('default_year');
        $element->setLabel('Default year'); // @translate
        $element->setValue($fieldData['default_year'] ?? null);
        $element->setAttributes([
            'step' => 1,
        ]);
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalSelect('default_month');
        $element->setLabel('Default month'); // @translate
        $element->setValue($fieldData['default_month'] ?? null);
        $element->setValueOptions(DatascribeElement\MonthSelect::MONTHS);
        $element->setEmptyOption('');
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalSelect('default_day');
        $element->setLabel('Default day'); // @translate
        $element->setValue($fieldData['default_day'] ?? null);
        $element->setValueOptions(DatascribeElement\DaySelect::DAYS);
        $element->setEmptyOption('');
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalSelect('default_hour');
        $element->setLabel('Default hour'); // @translate
        $element->setValue($fieldData['default_hour'] ?? null);
        $element->setValueOptions(DatascribeElement\HourSelect::HOURS);
        $element->setEmptyOption('');
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalSelect('default_minute');
        $element->setLabel('Default minute'); // @translate
        $element->setValue($fieldData['default_minute'] ?? null);
        $element->setValueOptions(DatascribeElement\MinuteSelect::MINUTES);
        $element->setEmptyOption('');
        $fieldset->add($element);

        $element = new DatascribeElement\OptionalSelect('default_second');
        $element->setLabel('Default second'); // @translate
        $element->setValue($fieldData['default_second'] ?? null);
        $element->setValueOptions(DatascribeElement\SecondSelect::SECONDS);
        $element->setEmptyOption('');
        $fieldset->add($element);
    }

    public function getFieldData(array $fieldFormData) : array
    {
        $fieldData = [];
        $fieldData['min_year'] =
            (isset($fieldFormData['min_year']) && preg_match('/^-?\d+$/', $fieldFormData['min_year']))
            ? $fieldFormData['min_year'] : null;
        $fieldData['max_year'] =
            (isset($fieldFormData['max_year']) && preg_match('/^-?\d+$/', $fieldFormData['max_year']))
            ? $fieldFormData['max_year'] : null;
        $fieldData['default_year'] =
            (isset($fieldFormData['default_year']) && preg_match('/^-?\d+$/', $fieldFormData['default_year']))
            ? $fieldFormData['default_year'] : null;
        $fieldData['default_month'] =
            (isset($fieldFormData['default_month']) && in_array($fieldFormData['default_month'], range(1, 12)))
            ? $fieldFormData['default_month'] : null;
        $fieldData['default_day'] =
            (isset($fieldFormData['default_day']) && in_array($fieldFormData['default_day'], range(1, 31)))
            ? $fieldFormData['default_day'] : null;
        $fieldData['default_hour'] =
            (isset($fieldFormData['default_hour']) && in_array($fieldFormData['default_hour'], range(0, 23)))
            ? $fieldFormData['default_hour'] : null;
        $fieldData['default_minute'] =
            (isset($fieldFormData['default_minute']) && in_array($fieldFormData['default_minute'], range(0, 59)))
            ? $fieldFormData['default_minute'] : null;
        $fieldData['default_second'] =
            (isset($fieldFormData['default_second']) && in_array($fieldFormData['default_second'], range(0, 59)))
            ? $fieldFormData['default_second'] : null;
        return $fieldData;
    }

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldData().
        return true;
    }

    public function addValueDataElements(Fieldset $fieldset, array $fieldData, array $valueData) : void
    {
        // Year
        $element = new DatascribeElement\YearSelect('year', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Year'); // @translate
        $value = null;
        if (isset($valueData['year'])) {
            $value = $valueData['year'];
        } elseif (isset($fieldData['default_year'])) {
            $value = $fieldData['default_year'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Month
        $element = new DatascribeElement\MonthSelect('month', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Month'); // @translate
        $value = null;
        if (isset($valueData['month'])) {
            $value = $valueData['month'];
        } elseif (isset($fieldData['default_month'])) {
            $value = $fieldData['default_month'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Day
        $element = new DatascribeElement\DaySelect('day', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Day'); // @translate
        $value = null;
        if (isset($valueData['day'])) {
            $value = $valueData['day'];
        } elseif (isset($fieldData['default_day'])) {
            $value = $fieldData['default_day'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Hour
        $element = new DatascribeElement\HourSelect('hour', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Hour'); // @translate
        $value = null;
        if (isset($valueData['hour'])) {
            $value = $valueData['hour'];
        } elseif (isset($fieldData['default_hour'])) {
            $value = $fieldData['default_hour'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Minute
        $element = new DatascribeElement\MinuteSelect('minute', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Minute'); // @translate
        $value = null;
        if (isset($valueData['minute'])) {
            $value = $valueData['minute'];
        } elseif (isset($fieldData['default_minute'])) {
            $value = $fieldData['default_minute'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Second
        $element = new DatascribeElement\SecondSelect('second', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Second'); // @translate
        $value = null;
        if (isset($valueData['second'])) {
            $value = $valueData['second'];
        } elseif (isset($fieldData['default_second'])) {
            $value = $fieldData['default_second'];
        }
        $element->setValue($value);
        $fieldset->add($element);
    }

    public function getValueData(array $valueFormData) : array
    {
        $valueData = [];
        $valueData['year'] = $valueFormData['year'] ?? null;
        $valueData['month'] = $valueFormData['month'] ?? null;
        $valueData['day'] = $valueFormData['day'] ?? null;
        $valueData['hour'] = $valueFormData['hour'] ?? null;
        $valueData['minute'] = $valueFormData['minute'] ?? null;
        $valueData['second'] = $valueFormData['second'] ?? null;

        // Make empty strings null so validation works.
        $valueData = array_map(function($value) {
            return (is_string($value) && ('' === trim($value))) ? null : $value;
        }, $valueData);

        return $valueData;
    }

    public function valueDataIsValid(array $fieldData, array $valueData) : bool
    {
        $isValid = function($element, $value) {
            if (null === $value) {
                return false;
            }
            $validatorChain = new ValidatorChain;
            foreach ($element->getValidators() as $validator) {
                $validatorChain->attach($validator);
            }
            return $validatorChain->isValid($value);
        };

        $yearSelect = new DatascribeElement\YearSelect('year', ['datascribe_field_data' => $fieldData]);
        $monthSelect = new DatascribeElement\MonthSelect('month', ['datascribe_field_data' => $fieldData]);
        $daySelect = new DatascribeElement\DaySelect('day', ['datascribe_field_data' => $fieldData]);
        $hourSelect = new DatascribeElement\HourSelect('hour', ['datascribe_field_data' => $fieldData]);
        $minuteSelect = new DatascribeElement\MinuteSelect('minute', ['datascribe_field_data' => $fieldData]);
        $secondSelect = new DatascribeElement\SecondSelect('second', ['datascribe_field_data' => $fieldData]);

        $return = true;
        if (isset($valueData['second'])) {
            $return = (
                $isValid($yearSelect, $valueData['year'])
                && $isValid($monthSelect, $valueData['month'])
                && $isValid($daySelect, $valueData['day'])
                && $isValid($hourSelect, $valueData['hour'])
                && $isValid($minuteSelect, $valueData['minute'])
                && $isValid($secondSelect, $valueData['second'])
            );
        } elseif (isset($valueData['minute'])) {
            $return = (
                $isValid($yearSelect, $valueData['year'])
                && $isValid($monthSelect, $valueData['month'])
                && $isValid($daySelect, $valueData['day'])
                && $isValid($hourSelect, $valueData['hour'])
                && $isValid($minuteSelect, $valueData['minute'])
            );
        } elseif (isset($valueData['hour'])) {
            $return = (
                $isValid($yearSelect, $valueData['year'])
                && $isValid($monthSelect, $valueData['month'])
                && $isValid($daySelect, $valueData['day'])
                && $isValid($hourSelect, $valueData['hour'])
            );
        } elseif (isset($valueData['day'])) {
            $return = (
                $isValid($yearSelect, $valueData['year'])
                && $isValid($monthSelect, $valueData['month'])
                && $isValid($daySelect, $valueData['day'])
            );
        } elseif (isset($valueData['month'])) {
            $return = (
                $isValid($yearSelect, $valueData['year'])
                && $isValid($monthSelect, $valueData['month'])
            );
        } elseif (isset($valueData['year'])) {
            $return = (
                $isValid($yearSelect, $valueData['year'])
            );
        }
        return $return;
    }

    public function getHtml(array $valueData) : string
    {
    }

    public function getValue(array $valueData) : string
    {
        if (isset($valueData['year'])) {
            $minusSign = '';
            $year = $valueData['year'];
            $yearArray = explode('-', $year);
            if (1 !== count($yearArray)) {
                $minusSign = '-';
                $year = (int) $yearArray[1];
            }
        }
        $return = '';
        if (isset($valueData['year']) && isset($valueData['month']) && isset($valueData['day']) && isset($valueData['hour']) && isset($valueData['minute']) && isset($valueData['second'])) {
            $return = sprintf(
                '%s%04d-%02d-%02dT%02d:%02d:%02d',
                $minusSign, $year, $valueData['month'], $valueData['day'],
                $valueData['hour'], $valueData['minute'], $valueData['second']
            );
        } elseif (isset($valueData['year']) && isset($valueData['month']) && isset($valueData['day']) && isset($valueData['hour']) && isset($valueData['minute'])) {
            $return = sprintf(
                '%s%04d-%02d-%02dT%02d:%02d',
                $minusSign, $year, $valueData['month'], $valueData['day'],
                $valueData['hour'], $valueData['minute']
            );
        } elseif (isset($valueData['year']) && isset($valueData['month']) && isset($valueData['day']) && isset($valueData['hour'])) {
            $return = sprintf(
                '%s%04d-%02d-%02dT%02d',
                $minusSign, $year, $valueData['month'], $valueData['day'],
                $valueData['hour']
            );
        } elseif (isset($valueData['year']) && isset($valueData['month']) && isset($valueData['day'])) {
            $return = sprintf(
                '%s%04d-%02d-%02d',
                $minusSign, $year, $valueData['month'], $valueData['day']
            );
        } elseif (isset($valueData['year']) && isset($valueData['month'])) {
            $return = sprintf(
                '%s%04d-%02d',
                $minusSign, $year, $valueData['month']
            );
        } elseif (isset($valueData['year'])) {
            $return = sprintf(
                '%s%04d',
                $minusSign, $year
            );
        }
        return $return;
    }
}
