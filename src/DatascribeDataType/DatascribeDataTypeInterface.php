<?php
namespace Datascribe\DatascribeDataType;

interface DatascribeDataTypeInterface
{
    /**
     * Get the name of this data type.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Get the description of this data type.
     *
     * @return string
     */
    public function getDescription() : ?string;

    /**
     * Get the form control for administering a field of this data type.
     *
     * @param array $fieldData
     * @return string
     */
    public function getFieldFormControl(array $fieldData) : string;

    /**
     * Get structured data from field form data (for storing).
     *
     * @param array $fieldFormData
     * @return array
     */
    public function getFieldData(array $fieldFormData) : array;

    /**
     * Get the form control for transcribing a value of this data type.
     *
     * @param array $valueData
     * @return string
     */
    public function getValueFormControl(array $valueData) : string;

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
