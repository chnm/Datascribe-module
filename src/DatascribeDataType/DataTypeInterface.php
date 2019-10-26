<?php
namespace Datascribe\DatascribeDataType;

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
     * Add elements used for administering a field of this data type to the
     * passed fieldset.
     *
     * @param Fieldset $fieldset
     * @param array $fieldData
     * @return string
     */
    public function addFieldElements(Fieldset $fieldset, array $fieldData) : void;

    /**
     * Get structured data from field form data (for storing).
     *
     * @param array $fieldFormData
     * @return array
     */
    public function getFieldData(array $fieldFormData) : array;

    /**
     * Add elements used for transcribing a value of this data type to the
     * passed fieldset.
     *
     * @param Fieldset $fieldset
     * @param array $valueData
     * @return string
     */
    public function addValueElements(Fieldset $fieldset, array $valueData) : void;

    /**
     * Get structured data from value form data (for storing).
     *
     * @param array $valueFormData
     * @return array
     */
    public function getValueData(array $valueFormData) : array;

    /**
     * Get the HTML value from value data (for rendering to page).
     *
     * @param array $valueData
     * @return string
     */
    public function getHtml(array $valueData) : string;

    /**
     * Get the raw value from value data (for export).
     *
     * @param array $valueData
     * @return string
     */
    public function getValue(array $valueData) : string;
}
