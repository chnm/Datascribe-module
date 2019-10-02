<?php
namespace Datascribe\Api\Representation;

use Omeka\Api\Representation\AbstractEntityRepresentation;

class DatascribeProjectRepresentation extends AbstractEntityRepresentation
{
    public function getJsonLdType()
    {
        return 'o-module-datascribe:Project';
    }

    public function getJsonLd()
    {
    }

    public function adminUrl($action = null, $canonical = false)
    {
        $url = $this->getViewHelper('Url');
        return $url(
            'admin/datascribe-project-id',
            [
                'action' => $action,
                'project-id' => $this->resource->getId(),
            ],
            ['force_canonical' => $canonical]
        );
    }

}
