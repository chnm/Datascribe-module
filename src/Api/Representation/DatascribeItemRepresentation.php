<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeItemRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-datascribe:Item';
    }

    public function getJsonLd()
    {
    }
}
