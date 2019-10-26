<?php
namespace Datascribe\Api\Adapter;

use Datascribe\Entity\DatascribeField;
use Doctrine\ORM\QueryBuilder;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Request;
use Omeka\Entity\EntityInterface;
use Omeka\Stdlib\ErrorStore;

class DatascribeDatasetAdapter extends AbstractEntityAdapter
{
    protected $sortFields = [
        'name' => 'name',
        'created' => 'created',
    ];

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
        if (isset($query['datascribe_project_id'])) {
            $alias = $this->createAlias();
            $qb->innerJoin('omeka_root.project', $alias);
            $qb->andWhere($qb->expr()->eq(
                "$alias.id",
                $this->createNamedParameter($qb, $query['datascribe_project_id']))
            );
        }
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

        $fieldCollection = $entity->getFields();
        $dataTypeManager = $this->getServiceLocator()->get('Datascribe\DataTypeManager');
        $position = 1;
        foreach ($request->getValue('o-module-datascribe:field', []) as $fieldFormData) {
            if (isset($fieldFormData['o:id']) && $fieldCollection->containsKey($fieldFormData['o:id'])) {
                // This field exists. Get it.
                $field = $fieldCollection->get($fieldFormData['o:id']);
            } elseif (isset($fieldFormData['o-module-datascribe:data_type']) && $dataTypeManager->has($fieldFormData['o-module-datascribe:data_type'])) {
                // This is a new field. Create it.
                $field = new DatascribeField;
                $field->setDataset($entity);
                $field->setDataType($fieldFormData['o-module-datascribe:data_type']);
                $fieldCollection->add($field);
            } else {
                // This field is in an invalid format. Ignore it.
                continue;
            }
            $dataType = $dataTypeManager->get($field->getDataType());
            $field->setData($dataType->getFieldData($fieldFormData));
            $field->setLabel($fieldFormData['o-module-datascribe:label']);
            $field->setHint($fieldFormData['o-module-datascribe:hint']);
            $field->setIsPrimary($fieldFormData['o-module-datascribe:is_primary']);
            $field->setPosition($position);
            $position++;
        }
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
