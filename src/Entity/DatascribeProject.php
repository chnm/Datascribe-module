<?php
namespace Datascribe\Entity;

use Omeka\Entity\AbstractEntity;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class DatascribeProject extends AbstractEntity
{
    use TraitId;
    use TraitNameDescription;
    use TraitCreatedOwnedBy;
}
