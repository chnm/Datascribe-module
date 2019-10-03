<?php
namespace Datascribe\Api\Adapter;

use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class DatascribeDatasetAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [];

    public function getResourceName()
    {
        return 'datascribe_datasets';
    }

    public function getRepresentationClass()
    {
        return 'Datascribe\Api\Representation\DatascribeDatasetRepresentation';
    }

    public function getEntityClass()
    {
        return 'Datascribe\Entity\DatascribeDataset';
    }

    public function buildQuery(QueryBuilder $qb, array $query)
    {
    }

    public function validateRequest(Request $request, ErrorStore $errorStore)
    {
        $data = $request->getContent();
        if (Request::CREATE === $request->getOperation()) {
            if (!isset($data['o-module-datascribe:project'])) {
                $errorStore->addError('o-module-datascribe:project', 'A dataset must have a project'); // @translate
            } elseif (!isset($data['o-module-datascribe:project']['o:id'])) {
                $errorStore->addError('o-module-datascribe:project', 'A project must have an ID'); // @translate
            }
        }
        if (isset($data['o:item_set']) && !isset($data['o:item_set']['o:id'])) {
            $errorStore->addError('o:item_set', 'An item set must have an ID'); // @translate
        }
        if (isset($data['o:owner']) && !isset($data['o:owner']['o:id'])) {
            $errorStore->addError('o:owner', 'An owner must have an ID'); // @translate
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        if ($this->shouldHydrate($request, 'o-module-datascribe:name')) {
            $entity->setName($request->getValue('o-module-datascribe:name'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:description')) {
            $entity->setDescription($request->getValue('o-module-datascribe:description'));
        }
        if ($this->shouldHydrate($request, 'o-module-datascribe:guidelines')) {
            $htmlPurifier = $this->getServiceLocator()->get('Omeka\HtmlPurifier');
            $entity->setGuidelines($htmlPurifier->purify($request->getValue('o-module-datascribe:guidelines')));
        }
        if ($this->shouldHydrate($request, 'o:is_public')) {
            $entity->setIsPublic($request->getValue('o:is_public', true));
        }
        if (Request::CREATE === $request->getOperation()) {
            $projectData = $request->getValue('o-module-datascribe:project');
            $project = $this->getAdapter('datascribe_projects')->findEntity($projectData['o:id']);
            $entity->setProject($project);
        }
        if ($this->shouldHydrate($request, 'o:item_set')) {
            $itemSet = $request->getValue('o:item_set');
            if ($itemSet) {
                $itemSet = $this->getAdapter('item_sets')->findEntity($itemSet['o:id']);
            }
            $entity->setItemSet($itemSet);
        }
        $this->hydrateOwner($request, $entity);
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        if (!$this->isUnique($entity, ['name' => $entity->getName()])) {
            $errorStore->addError('o-module-datascribe:name', new Message(
                'The name "%s" is already taken.', // @translate
                $entity->getName()
            ));
        }
        if (null === $entity->getName()) {
            $errorStore->addError('o-module-datascribe:name', 'A dataset name must not be null'); // @translate
        }
        if (null === $entity->getProject()) {
            $errorStore->addError('o-module-datascribe:project', 'A project must not be null'); // @translate
        }
    }
}
