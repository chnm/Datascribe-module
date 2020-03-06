<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Fieldset;

class Datetime extends AbstractDatetime
{
    public function getLabel() : string
    {
        return 'DateTime'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $this->addDateFieldElements($fieldset, $fieldData);
        $this->addTimeFieldElements($fieldset, $fieldData);
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        return array_merge(
            $this->getDateFieldDataFromUserData($userData),
            $this->getTimeFieldDataFromUserData($userData)
        );
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $array = $this->getDateTimeArray($valueText);
        $this->addDateValueElements($fieldset, $fieldData, $valueText, $array);
        $this->addTimeValueElements($fieldset, $fieldData, $valueText, $array);
    }

    public function getValueTextFromUserData(array $userData) : ?string
    {
        $array = [
            'year' => $userData['year'] ?? null,
            'month' => $userData['month'] ?? null,
            'day' => $userData['day'] ?? null,
            'hour' => $userData['hour'] ?? null,
            'minute' => $userData['minute'] ?? null,
            'second' => $userData['second'] ?? null,
        ];
        return $this->getDateTimeString($this->emptyValuesToNull($array));
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        $array = $this->getDateTimeArray($valueText);

        $yearSelect = new DatascribeElement\YearSelect('year', ['datascribe_field_data' => $fieldData]);
        $monthSelect = new DatascribeElement\MonthSelect('month', ['datascribe_field_data' => $fieldData]);
        $daySelect = new DatascribeElement\DaySelect('day', ['datascribe_field_data' => $fieldData]);
        $hourSelect = new DatascribeElement\HourSelect('hour', ['datascribe_field_data' => $fieldData]);
        $minuteSelect = new DatascribeElement\MinuteSelect('minute', ['datascribe_field_data' => $fieldData]);
        $secondSelect = new DatascribeElement\SecondSelect('second', ['datascribe_field_data' => $fieldData]);

        if (is_numeric($array['second'])) {
            return (
                $this->isValid($yearSelect, $array['year'])
                && $this->isValid($monthSelect, $array['month'])
                && $this->isValid($daySelect, $array['day'])
                && $this->isValid($hourSelect, $array['hour'])
                && $this->isValid($minuteSelect, $array['minute'])
                && $this->isValid($secondSelect, $array['second'])
            );
        }
        if (is_numeric($array['minute'])) {
            return (
                $this->isValid($yearSelect, $array['year'])
                && $this->isValid($monthSelect, $array['month'])
                && $this->isValid($daySelect, $array['day'])
                && $this->isValid($hourSelect, $array['hour'])
                && $this->isValid($minuteSelect, $array['minute'])
            );
        }
        if (is_numeric($array['hour'])) {
            return (
                $this->isValid($yearSelect, $array['year'])
                && $this->isValid($monthSelect, $array['month'])
                && $this->isValid($daySelect, $array['day'])
                && $this->isValid($hourSelect, $array['hour'])
            );
        }
        if (is_numeric($array['day'])) {
            return (
                $this->isValid($yearSelect, $array['year'])
                && $this->isValid($monthSelect, $array['month'])
                && $this->isValid($daySelect, $array['day'])
            );
        }
        if (is_numeric($array['month'])) {
            return (
                $this->isValid($yearSelect, $array['year'])
                && $this->isValid($monthSelect, $array['month'])
            );
        }
        if (is_numeric($array['year'])) {
            return (
                $this->isValid($yearSelect, $array['year'])
            );
        }
        return true;
    }

    /**
     * Get an array of date/time data given an ISO 8601 string.
     *
     * @param ?string $string
     * @return array
     */
    protected function getDateTimeArray(?string $string)
    {
        $regex = sprintf('^%s(T%s)?$', self::REGEX_ISO8601_DATE, self::REGEX_ISO8601_TIME);
        preg_match(sprintf('/%s/', $regex), $string, $matches);
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
     * @param array $array
     * @return string
     */
    protected function getDateTimeString(array $array)
    {
        if (isset($array['year'])) {
            preg_match('/^(-)?(\d+)$/', $array['year'], $matches);
            $year = [$matches[1] ?? null, $matches[2]];
        }
        if (isset($array['year']) && isset($array['month']) && isset($array['day'])
            && isset($array['hour']) && isset($array['minute']) && isset($array['second'])
        ) {
            return sprintf(
                '%s%04d-%02d-%02dT%02d:%02d:%02d',
                $year[0], $year[1], $array['month'], $array['day'],
                $array['hour'], $array['minute'], $array['second']
            );
        }
        if (isset($array['year']) && isset($array['month']) && isset($array['day'])
            && isset($array['hour']) && isset($array['minute'])
        ) {
            return sprintf(
                '%s%04d-%02d-%02dT%02d:%02d',
                $year[0], $year[1], $array['month'], $array['day'],
                $array['hour'], $array['minute']
            );
        }
        if (isset($array['year']) && isset($array['month']) && isset($array['day'])
            && isset($array['hour'])
        ) {
            return sprintf(
                '%s%04d-%02d-%02dT%02d',
                $year[0], $year[1], $array['month'], $array['day'],
                $array['hour']
            );
        }
        if (isset($array['year']) && isset($array['month']) && isset($array['day'])) {
            return sprintf(
                '%s%04d-%02d-%02d',
                $year[0], $year[1], $array['month'], $array['day']
            );
        }
        if (isset($array['year']) && isset($array['month'])
        ) {
            return sprintf(
                '%s%04d-%02d',
                $year[0], $year[1], $array['month']
            );
        }
        if (isset($array['year'])) {
            return sprintf(
                '%s%04d',
                $year[0], $year[1]
            );
        }
        return false;
    }
}
