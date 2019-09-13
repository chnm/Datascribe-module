<?php
namespace Datascribe\FieldDataType;

class Text implements FieldDataTypeInterface
{
    public function getLabel() : string
    {
        return 'Text';
    }

    public function getDescription() : ?string
    {
    }

    public function getFieldFormControl(array $fieldData) : string
    {
    }

    public function getFieldData(array $fieldFormData) : array
    {
    }

    public function getValueFormControl(array $valueData) : string
    {
    }

    public function getValueData(array $valueFormData) : array
    {
    }

    public function getHtml(array $valueData) : string
    {
    }

    public function getValue(array $valueData) : string
    {
    }
}
