<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeDatasetRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-datascribe:Dataset';
    }

    public function getJsonLd()
    {
    }
}
