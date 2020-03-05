<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Fieldset;
use Zend\Validator\ValidatorChain;

abstract class AbstractDatetime implements DataTypeInterface
{
    const REGEX_ISO8601_DATE = '(-)?(\d{4})(-(\d{2})(-(\d{2}))?)?';
    const REGEX_ISO8601_TIME = '(\d{2})(:(\d{2})(:(\d{2}))?)?';

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data has already been filtered out.
        return true;
    }

    protected function emptyStringToNull(array $array)
    {
        // Make empty strings null so validation works.
        return array_map(function($value) {
            return (is_string($value) && ('' === trim($value))) ? null : $value;
        }, $array);
    }

    protected function isValid($element, $text)
    {
        if (null === $text) {
            return false;
        }
        $validatorChain = new ValidatorChain;
        foreach ($element->getValidators() as $validator) {
            $validatorChain->attach($validator);
        }
        return $validatorChain->isValid($text);
    }

    protected function addDateFieldElements(Fieldset $fieldset, array $fieldData) : void
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
    }

    protected function addTimeFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
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

    protected function getDateFieldDataFromUserData(array $userData) : array
    {
        $fieldData = [];
        $fieldData['min_year'] =
            (isset($userData['min_year']) && preg_match('/^-?\d+$/', $userData['min_year']))
            ? $userData['min_year'] : null;
        $fieldData['max_year'] =
            (isset($userData['max_year']) && preg_match('/^-?\d+$/', $userData['max_year']))
            ? $userData['max_year'] : null;
        $fieldData['default_year'] =
            (isset($userData['default_year']) && preg_match('/^-?\d+$/', $userData['default_year']))
            ? $userData['default_year'] : null;
        $fieldData['default_month'] =
            (isset($userData['default_month']) && in_array($userData['default_month'], range(1, 12)))
            ? $userData['default_month'] : null;
        $fieldData['default_day'] =
            (isset($userData['default_day']) && in_array($userData['default_day'], range(1, 31)))
            ? $userData['default_day'] : null;
        return $fieldData;
    }

    protected function getTimeFieldDataFromUserData(array $userData) : array
    {
        $fieldData = [];
        $fieldData['default_hour'] =
            (isset($userData['default_hour']) && in_array($userData['default_hour'], range(0, 23)))
            ? $userData['default_hour'] : null;
        $fieldData['default_minute'] =
            (isset($userData['default_minute']) && in_array($userData['default_minute'], range(0, 59)))
            ? $userData['default_minute'] : null;
        $fieldData['default_second'] =
            (isset($userData['default_second']) && in_array($userData['default_second'], range(0, 59)))
            ? $userData['default_second'] : null;
        return $fieldData;
    }

    public function addDateValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText, array $array) : void
    {
        // Year
        $element = new DatascribeElement\YearSelect('year', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Year'); // @translate
        $value = null;
        if (is_null($valueText) && isset($fieldData['default_year'])) {
            $value = $fieldData['default_year'];
        } elseif (is_numeric($array['year'])) {
            $value = $array['year'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Month
        $element = new DatascribeElement\MonthSelect('month', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Month'); // @translate
        $value = null;
        if (is_null($valueText) && isset($fieldData['default_month'])) {
            $value = $fieldData['default_month'];
        } elseif (is_numeric($array['month'])) {
            $value = $array['month'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Day
        $element = new DatascribeElement\DaySelect('day', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Day'); // @translate
        $value = null;
        if (is_null($valueText) && isset($fieldData['default_day'])) {
            $value = $fieldData['default_day'];
        } elseif (is_numeric($array['day'])) {
            $value = $array['day'];
        }
        $element->setValue($value);
        $fieldset->add($element);
    }

    public function addTimeValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText, array $array) : void
    {
        // Hour
        $element = new DatascribeElement\HourSelect('hour', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Hour'); // @translate
        $value = null;
        if (is_null($valueText) && isset($fieldData['default_hour'])) {
            $value = $fieldData['default_hour'];
        } elseif (is_numeric($array['hour'])) {
            $value = $array['hour'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Minute
        $element = new DatascribeElement\MinuteSelect('minute', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Minute'); // @translate
        $value = null;
        if (is_null($valueText) && isset($fieldData['default_minute'])) {
            $value = $fieldData['default_minute'];
        } elseif (is_numeric($array['minute'])) {
            $value = $array['minute'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Second
        $element = new DatascribeElement\SecondSelect('second', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Second'); // @translate
        $value = null;
        if (is_null($valueText) && isset($fieldData['default_second'])) {
            $value = $fieldData['default_second'];
        } elseif (is_numeric($array['second'])) {
            $value = $array['second'];
        }
        $element->setValue($value);
        $fieldset->add($element);
    }
}
