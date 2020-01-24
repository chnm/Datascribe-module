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
     * @return string
     */
    public function addFieldDataElements(Fieldset $fieldset, array $fieldData) : void;

    /**
     * Get structured data from field form data (for storing).
     *
     * @param array $fieldFormData
     * @return array
     */
    public function getFieldData(array $fieldFormData) : array;

    /**
     * Get the form element used for the value data.
     *
     * @param array $fieldData
     * @param array $valueData
     * @return Element
     */
    public function getValueDataElement(array $fieldData, array $valueData) : Element;

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
