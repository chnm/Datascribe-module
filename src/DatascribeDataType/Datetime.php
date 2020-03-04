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

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
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

    public function getFieldDataFromUserData(array $userData) : array
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

    public function fieldDataIsValid(array $fieldData) : bool
    {
        // Invalid data was filtered out in self::getFieldDataFromUserData().
        return true;
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $dateTime = $this->getDateTimeArray($valueText);

        // Year
        $element = new DatascribeElement\YearSelect('year', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Year'); // @translate
        $value = null;
        if (is_null($valueText) && isset($fieldData['default_year'])) {
            $value = $fieldData['default_year'];
        } elseif (is_numeric($dateTime['year'])) {
            $value = $dateTime['year'];
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
        } elseif (is_numeric($dateTime['month'])) {
            $value = $dateTime['month'];
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
        } elseif (is_numeric($dateTime['day'])) {
            $value = $dateTime['day'];
        }
        $element->setValue($value);
        $fieldset->add($element);

        // Hour
        $element = new DatascribeElement\HourSelect('hour', [
            'datascribe_field_data' => $fieldData,
        ]);
        $element->setLabel('Hour'); // @translate
        $value = null;
        if (is_null($valueText) && isset($fieldData['default_hour'])) {
            $value = $fieldData['default_hour'];
        } elseif (is_numeric($dateTime['hour'])) {
            $value = $dateTime['hour'];
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
        } elseif (is_numeric($dateTime['minute'])) {
            $value = $dateTime['minute'];
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
        } elseif (is_numeric($dateTime['second'])) {
            $value = $dateTime['second'];
        }
        $element->setValue($value);
        $fieldset->add($element);
    }

    public function getValueTextFromUserData(array $userData) : string
    {
        $dateTime = [
            'year' => $userData['year'] ?? null,
            'month' => $userData['month'] ?? null,
            'day' => $userData['day'] ?? null,
            'hour' => $userData['hour'] ?? null,
            'minute' => $userData['minute'] ?? null,
            'second' => $userData['second'] ?? null,
        ];

        // Make empty strings null so validation works.
        $dateTime = array_map(function($value) {
            return (is_string($value) && ('' === trim($value))) ? null : $value;
        }, $dateTime);

        return $this->getDateTimeString($dateTime);
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        $dateTime = $this->getDateTimeArray($valueText);

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

        if (is_numeric($dateTime['second'])) {
            return (
                $isValid($yearSelect, $dateTime['year'])
                && $isValid($monthSelect, $dateTime['month'])
                && $isValid($daySelect, $dateTime['day'])
                && $isValid($hourSelect, $dateTime['hour'])
                && $isValid($minuteSelect, $dateTime['minute'])
                && $isValid($secondSelect, $dateTime['second'])
            );
        }
        if (is_numeric($dateTime['minute'])) {
            return (
                $isValid($yearSelect, $dateTime['year'])
                && $isValid($monthSelect, $dateTime['month'])
                && $isValid($daySelect, $dateTime['day'])
                && $isValid($hourSelect, $dateTime['hour'])
                && $isValid($minuteSelect, $dateTime['minute'])
            );
        }
        if (is_numeric($dateTime['hour'])) {
            return (
                $isValid($yearSelect, $dateTime['year'])
                && $isValid($monthSelect, $dateTime['month'])
                && $isValid($daySelect, $dateTime['day'])
                && $isValid($hourSelect, $dateTime['hour'])
            );
        }
        if (is_numeric($dateTime['day'])) {
            return (
                $isValid($yearSelect, $dateTime['year'])
                && $isValid($monthSelect, $dateTime['month'])
                && $isValid($daySelect, $dateTime['day'])
            );
        }
        if (is_numeric($dateTime['month'])) {
            return (
                $isValid($yearSelect, $dateTime['year'])
                && $isValid($monthSelect, $dateTime['month'])
            );
        }
        if (is_numeric($dateTime['year'])) {
            return (
                $isValid($yearSelect, $dateTime['year'])
            );
        }
        return true;
    }

    /**
     * Get an array of date/time data given an ISO 8601 string.
     *
     * @param ?string $dateTimeString
     * @return array
     */
    protected function getDateTimeArray(?string $dateTimeString)
    {
        $regexDate = '(-)?(\d{4})(-(\d{2})(-(\d{2}))?)?';
        $regexTime = '(\d{2})(:(\d{2})(:(\d{2}))?)?';
        $regexDateTime = sprintf('^%s(T%s)?$', $regexDate, $regexTime);
        preg_match(sprintf('/%s/', $regexDateTime), $dateTimeString, $matches);
        $year = sprintf('%s%s', $matches[1] ?? '', $matches[2] ?? '');
        return [
            'year' => $year ? (int) $year : null,
            'month' => isset($matches[4]) ? (int) $matches[4] : null,
            'day' => isset($matches[6]) ? (int) $matches[6] : null,
            'hour' => isset($matches[8]) ? (int) $matches[8] : null,
            'minute' => isset($matches[10]) ? (int) $matches[10] : null,
            'second' => isset($matches[12]) ? (int) $matches[12] : null,
        ];
    }

    /**
     * Get an ISO 8601 string given an array of date/time data.
     *
     * @param array $dateTimeArray
     * @return string
     */
    protected function getDateTimeString(array $dateTimeArray)
    {
        if (isset($dateTimeArray['year'])) {
            preg_match('/^(-)?(\d+)$/', $dateTimeArray['year'], $matches);
            $year = [
                $matches[1] ?? null,
                $matches[2]
            ];
        }
        if (isset($dateTimeArray['year']) && isset($dateTimeArray['month']) && isset($dateTimeArray['day']) && isset($dateTimeArray['hour']) && isset($dateTimeArray['minute']) && isset($dateTimeArray['second'])) {
            return sprintf(
                '%s%04d-%02d-%02dT%02d:%02d:%02d',
                $year[0], $year[1], $dateTimeArray['month'], $dateTimeArray['day'],
                $dateTimeArray['hour'], $dateTimeArray['minute'], $dateTimeArray['second']
            );
        }
        if (isset($dateTimeArray['year']) && isset($dateTimeArray['month']) && isset($dateTimeArray['day']) && isset($dateTimeArray['hour']) && isset($dateTimeArray['minute'])) {
            return sprintf(
                '%s%04d-%02d-%02dT%02d:%02d',
                $year[0], $year[1], $dateTimeArray['month'], $dateTimeArray['day'],
                $dateTimeArray['hour'], $dateTimeArray['minute']
            );
        }
        if (isset($dateTimeArray['year']) && isset($dateTimeArray['month']) && isset($dateTimeArray['day']) && isset($dateTimeArray['hour'])) {
            return sprintf(
                '%s%04d-%02d-%02dT%02d',
                $year[0], $year[1], $dateTimeArray['month'], $dateTimeArray['day'],
                $dateTimeArray['hour']
            );
        }
        if (isset($dateTimeArray['year']) && isset($dateTimeArray['month']) && isset($dateTimeArray['day'])) {
            return sprintf(
                '%s%04d-%02d-%02d',
                $year[0], $year[1], $dateTimeArray['month'], $dateTimeArray['day']
            );
        }
        if (isset($dateTimeArray['year']) && isset($dateTimeArray['month'])) {
            return sprintf(
                '%s%04d-%02d',
                $year[0], $year[1], $dateTimeArray['month']
            );
        }
        if (isset($dateTimeArray['year'])) {
            return sprintf(
                '%s%04d',
                $year[0], $year[1]
            );
        }
        return false;
    }
}
