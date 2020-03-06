<?php
namespace Datascribe\DatascribeDataType;

use Datascribe\Form\Element as DatascribeElement;
use Zend\Form\Fieldset;

class Time extends AbstractDatetime
{
    public function getLabel() : string
    {
        return 'Time'; // @translate
    }

    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void
    {
        $this->addTimeFieldElements($fieldset, $fieldData);
    }

    public function getFieldDataFromUserData(array $userData) : array
    {
        return $this->getTimeFieldDataFromUserData($userData);
    }

    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void
    {
        $array = $this->getTimeArray($valueText);
        $this->addTimeValueElements($fieldset, $fieldData, $valueText, $array);
    }

    public function getValueTextFromUserData(array $userData) : ?string
    {
        $array = [
            'hour' => $userData['hour'] ?? null,
            'minute' => $userData['minute'] ?? null,
            'second' => $userData['second'] ?? null,
        ];
        return $this->getTimeString($this->emptyValuesToNull($array));
    }

    public function valueTextIsValid(array $fieldData, ?string $valueText) : bool
    {
        $array = $this->getTimeArray($valueText);

        $hourSelect = new DatascribeElement\HourSelect('hour', ['datascribe_field_data' => $fieldData]);
        $minuteSelect = new DatascribeElement\MinuteSelect('minute', ['datascribe_field_data' => $fieldData]);
        $secondSelect = new DatascribeElement\SecondSelect('second', ['datascribe_field_data' => $fieldData]);

        if (is_numeric($array['second'])) {
            return (
                $this->isValid($hourSelect, $array['hour'])
                && $this->isValid($minuteSelect, $array['minute'])
                && $this->isValid($secondSelect, $array['second'])
            );
        }
        if (is_numeric($array['minute'])) {
            return (
                $this->isValid($hourSelect, $array['hour'])
                && $this->isValid($minuteSelect, $array['minute'])
            );
        }
        if (is_numeric($array['hour'])) {
            return (
                $this->isValid($hourSelect, $array['hour'])
            );
        }
        return true;
    }

    /**
     * Get an array of time data given an ISO 8601 string.
     *
     * @param ?string $string
     * @return array
     */
    protected function getTimeArray(?string $string)
    {
        $regex = sprintf('^%s?$', self::REGEX_ISO8601_TIME);
        preg_match(sprintf('/%s/', $regex), $string, $matches);
        return [
            'hour' => isset($matches[1]) ? (int) $matches[1] : null,
            'minute' => isset($matches[3]) ? (int) $matches[3] : null,
            'second' => isset($matches[5]) ? (int) $matches[5] : null,
        ];
    }

    /**
     * Get an ISO 8601 string given an array of time data.
     *
     * @param array $array
     * @return string
     */
    protected function getTimeString(array $array)
    {
        if (isset($array['hour']) && isset($array['minute']) && isset($array['second'])) {
            return sprintf('%02d:%02d:%02d', $array['hour'], $array['minute'], $array['second']);
        }
        if (isset($array['hour']) && isset($array['minute'])) {
            return sprintf('%02d:%02d', $array['hour'], $array['minute']);
        }
        if (isset($array['hour'])) {
            return sprintf('%02d', $array['hour']);
        }
        return false;
    }
}
