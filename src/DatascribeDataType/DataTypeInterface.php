<?php
namespace Datascribe\DatascribeDataType;

use Zend\Form\Element;
use Zend\Form\Fieldset;

interface DataTypeInterface
{
    /**
     * Get the label of this data type.
     *
     * @return string
     */
    public function getLabel() : string;

    /**
     * Add the form elements used for the field data.
     *
     * @param Fieldset $fieldset
     * @param array $fieldData
     */
    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void;

    /**
     * Get field data from user data (for storing).
     *
     * @param array $userData
     * @return array
     */
    public function getFieldDataFromUserData(array $userData) : array;

    /**
     * Is the field data valid?
     *
     * @param array $fieldData
     * @return bool
     */
    public function fieldDataIsValid(array $fieldData) : bool;

    /**
     * Add the form elements used for the value.
     *
     * @param Fieldset $fieldset
     * @param array $fieldData
     * @param string $valueText
     */
    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void;

    /**
     * Get value text from user data (for storing).
     *
     * @param array $userData
     * @return string
     */
    public function getValueTextFromUserData(array $userData) : string;

    /**
     * Is the value text valid?
     *
     * @param array $fieldData
     * @param string $valueText
     * @return bool
     */
    public function valueTextIsValid(array $fieldData, string $valueText) : bool;
}
