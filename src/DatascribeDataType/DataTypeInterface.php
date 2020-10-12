<?php
namespace Datascribe\DatascribeDataType;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;

interface DataTypeInterface
{
    /**
     * Get the label of this data type.
     *
     * @see Datascribe\Form\DatasetForm
     * @return string
     */
    public function getLabel() : string;

    /**
     * Add the form elements used for the field data.
     *
     * @see Datascribe\Form\DatasetForm
     * @param Fieldset $fieldset
     * @param array $fieldData
     */
    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void;

    /**
     * Get field data from user data (for storing).
     *
     * @see Datascribe\Api\Adapter\DatascribeDatasetAdapter
     * @param array $userData
     * @return array
     */
    public function getFieldDataFromUserData(array $userData) : array;

    /**
     * Is the field data valid?
     *
     * @see Datascribe\Api\Adapter\DatascribeDatasetAdapter
     * @param array $fieldData
     * @return bool
     */
    public function fieldDataIsValid(array $fieldData) : bool;

    /**
     * Add the form elements used for the value.
     *
     * @see Datascribe\Form\RecordForm
     * @param Fieldset $fieldset
     * @param array $fieldData
     * @param string $valueText
     */
    public function addValueElements(Fieldset $fieldset, array $fieldData, ?string $valueText) : void;

    /**
     * Get value text from user data (for storing).
     *
     * @see Datascribe\Api\Adapter\DatascribeRecordAdapter
     * @param array $userData
     * @return ?string
     */
    public function getValueTextFromUserData(array $userData) : ?string;

    /**
     * Is the value text valid?
     *
     * @see Datascribe\Form\RecordForm
     * @see Datascribe\Api\Adapter\DatascribeRecordAdapter
     * @see Datascribe\Api\Representation\DatascribeValueRepresentation
     * @param array $fieldData
     * @param string $valueText
     * @return bool
     */
    public function valueTextIsValid(array $fieldData, string $valueText) : bool;
}
