<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Fieldset;

class Date extends AbstractDatetime
{
    public function getLabel() : string
    {
        return 'Date'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $this->addDateFieldElements($fieldset, $fieldData);
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        return $this->getDateFieldDataFromUserData($userData);
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $array = $this->getDateArray($valueText);
        $this->addDateValueElements($fieldset, $fieldData, $valueText, $array);
    }

    public function getValueTextFromUserData(array $userData) : ?string
    {
        $array = [
            'year' => $userData['year'] ?? null,
            'month' => $userData['month'] ?? null,
            'day' => $userData['day'] ?? null,
        ];
        return $this->getDateString($this->emptyValuesToNull($array));
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        $array = $this->getDateArray($valueText);

        $yearSelect = new DatascribeElement\YearSelect('year', ['datascribe_field_data' => $fieldData]);
        $monthSelect = new DatascribeElement\MonthSelect('month', ['datascribe_field_data' => $fieldData]);
        $daySelect = new DatascribeElement\DaySelect('day', ['datascribe_field_data' => $fieldData]);

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
     * Get an array of date data given an ISO 8601 string.
     *
     * @param ?string $string
     * @return array
     */
    protected function getDateArray(?string $string)
    {
        $regex = sprintf('^%s?$', self::REGEX_ISO8601_DATE);
        preg_match(sprintf('/%s/', $regex), $string, $matches);
        $year = sprintf('%s%s', $matches[1] ?? '', $matches[2] ?? '');
        return [
            'year' => $year ? (int) $year : null,
            'month' => isset($matches[4]) ? (int) $matches[4] : null,
            'day' => isset($matches[6]) ? (int) $matches[6] : null,
        ];
    }

    /**
     * Get an ISO 8601 string given an array of date data.
     *
     * @param array $array
     * @return string
     */
    protected function getDateString(array $array)
    {
        if (isset($array['year'])) {
            preg_match('/^(-)?(\d+)$/', $array['year'], $matches);
            $year = [$matches[1] ?? null, $matches[2]];
        }
        if (isset($array['year']) && isset($array['month']) && isset($array['day'])) {
            return sprintf('%s%04d-%02d-%02d', $year[0], $year[1], $array['month'], $array['day']);
        }
        if (isset($array['year']) && isset($array['month'])) {
            return sprintf('%s%04d-%02d', $year[0], $year[1], $array['month']);
        }
        if (isset($array['year'])) {
            return sprintf('%s%04d', $year[0], $year[1]);
        }
        return false;
    }
}
