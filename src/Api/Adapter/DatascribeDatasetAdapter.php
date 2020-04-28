<?php
namespace Datascribe\Api\Adapter;

use Datascribe\DatascribeDataType\Unknown;
use Datascribe\Entity\DatascribeField;
use DateTime;
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
            if (!isset($data['o-module-datascribe:project'])
                || !isset($data['o-module-datascribe:project']['o:id'])
                || !is_numeric($data['o-module-datascribe:project']['o:id'])
            ) {
                $errorStore->addError('o-module-datascribe:project', 'Invalid project format passed in request.'); // @translate
            }
        }
        if (isset($data['o:item_set']) && !isset($data['o:item_set']['o:id'])) {
            $errorStore->addError('o:item_set', 'Invalid item set format passed in request.'); // @translate
        }
        if (isset($data['o:owner']) && !isset($data['o:owner']['o:id'])) {
            $errorStore->addError('o:owner', 'Invalid owner format passed in request.'); // @translate
        }
        if (isset($data['o-module-datascribe:field'])) {
            if (!is_array($data['o-module-datascribe:field'])) {
                $errorStore->addError('o-module-datascribe:field', 'Invalid fields format passed in request.'); // @translate
            } else {
                foreach ($data['o-module-datascribe:field'] as $fieldId => $fieldData) {
                    if (!isset($fieldData['name'])) {
                        $errorStore->addError('name', sprintf('Invalid field format passed in request. Missing "name" for field #%s.', $fieldId));
                    }
                    if (!isset($fieldData['description'])) {
                        $errorStore->addError('description', sprintf('Invalid field format passed in request. Missing "description" for field #%s.', $fieldId));
                    }
                    if (!isset($fieldData['is_primary'])) {
                        $errorStore->addError('is_primary', sprintf('Invalid field format passed in request. Missing "is_primary" for field #%s.', $fieldId));
                    }
                    if (!isset($fieldData['is_required'])) {
                        $errorStore->addError('is_required', sprintf('Invalid field format passed in request. Missing "is_required" for field #%s.', $fieldId));
                    }
                    if (isset($fieldData['data']) && !is_array($fieldData['data'])) {
                        $errorStore->addError('data', sprintf('Invalid field format passed in request. Invalid "data" format for field #%s.', $fieldId));
                    }
                }
            }
        }
    }

    public function hydrate(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $services = $this->getServiceLocator();
        $user = $services->get('Omeka\AuthenticationService')->getIdentity();

        $this->hydrateOwner($request, $entity);
        if (Request::CREATE === $request->getOperation()) {
            $entity->setCreatedBy($user);
        } else {
            $entity->setModifiedBy($user);
            $entity->setModified(new DateTime('now'));
        }
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
        if ($this->shouldHydrate($request, 'o-module-datascribe:field')) {
            $this->hydrateFields($request, $entity, $errorStore);
        }
    }

    protected function hydrateFields(Request $request, EntityInterface $entity, ErrorStore $errorStore)
    {
        $services = $this->getServiceLocator();
        $em = $services->get('Omeka\EntityManager');
        $dataTypes = $services->get('Datascribe\DataTypeManager');

        $fields = $entity->getFields();
        $fieldsToRetain = [];
        $position = 1;

        // Update existing fields and create new fields.
        foreach ($request->getValue('o-module-datascribe:field', []) as $fieldId => $fieldData) {
            $field = $em->getReference('Datascribe\Entity\DatascribeField', $fieldId);
            if ($fields->containsKey($fieldId)) {
                // This is an existing field.
                $field = $fields->get($fieldId);
                $fieldsToRetain[] = $field->getId();
            } elseif (isset($fieldData['data_type']) && $dataTypes->has($fieldData['data_type'])) {
                // This is a new field.
                $field = new DatascribeField;
                $field->setDataset($entity);
                $field->setDataType($fieldData['data_type']);
                $fields->add($field);
            } else {
                // This field is in an invalid format.
                continue;
            }

            $name =
                (isset($fieldData['name']) && preg_match('/^.+$/', $fieldData['name']))
                ? $fieldData['name'] : null;
            $description =
                (isset($fieldData['description']) && preg_match('/^.+$/', $fieldData['description']))
                ? $fieldData['description'] : null;

            $field->setName($name);
            $field->setDescription($description);
            $field->setIsPrimary($fieldData['is_primary'] ?? false);
            $field->setIsRequired($fieldData['is_required'] ?? false);
            $field->setPosition($position++);
            $dataType = $dataTypes->get($field->getDataType());
            if (!($dataType instanceof Unknown)) {
                // Set field data only when the data type is known.
                $data = $fieldData['data'] ?? [];
                $field->setData($dataType->getFieldDataFromUserData($data));
            }
        }

        // Remove fields not passed in the request.
        foreach ($fields as $field) {
            if ($field->getId() && !in_array($field->getId(), $fieldsToRetain)) {
                $fields->removeElement($field);
            }
        }
    }

    public function validateEntity(EntityInterface $entity, ErrorStore $errorStore)
    {
        $services = $this->getServiceLocator();
        $dataTypes = $services->get('Datascribe\DataTypeManager');

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
        foreach ($entity->getFields() as $field) {
            // Validate the field data.
            $dataType = $dataTypes->get($field->getDataType());
            if (!$dataType->fieldDataIsValid($field->getData())) {
                $errorStore->addError('data', sprintf('Invalid field data for field "%s".', $field->getName())); // @translate
            }
        }
    }
}
