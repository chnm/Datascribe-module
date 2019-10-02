<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeRecordRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-datascribe:Record';
    }

    public function getJsonLd()
    {
    }
}
