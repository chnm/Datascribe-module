<?php
namespace Datascribe\Job;

use Datascribe\Entity\DatascribeDataset;
use Datascribe\Entity\DatascribeValue;
use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;

class ValidateDataset extends AbstractJob
{
    /**
     * Validate a dataset.
     */
    public function perform()
    {
        if (!is_numeric($this->getArg('datascribe_dataset_id'))) {
            throw new Exception\RuntimeException('Missing datascribe_dataset_id');
        }
        $services = $this->getServiceLocator();
        $em = $services->get('Omeka\EntityManager');
        $dataset = $em->find(DatascribeDataset::class, $this->getArg('datascribe_dataset_id'));
        if (null === $dataset) {
            throw new Exception\RuntimeException('Cannot find dataset');
        }

        // When a user adds a field to a dataset, any records existing before
        // that time will have uncreated values for that field. Here we create
        // those uncreated values so they can be validated.
        $sql = '
            SELECT field.id AS field_id, record.id AS record_id
            FROM datascribe_dataset dataset
            INNER JOIN datascribe_item item ON item.dataset_id = dataset.id
            INNER JOIN datascribe_record record ON record.item_id = item.id
            INNER JOIN datascribe_field field ON field.dataset_id = dataset.id
            LEFT JOIN datascribe_value `value` ON (`value`.record_id = record.id AND `value`.field_id = field.id)
            WHERE dataset.id = :datasetId
            AND value.id IS NULL
            LIMIT :limit';
        $conn = $services->get('Omeka\Connection');
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('datasetId', $dataset->getId(), \PDO::PARAM_INT);
        $stmt->bindValue('limit', 100, \PDO::PARAM_INT);
        do {
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $result) {
                $field = $em->find('Datascribe\Entity\DatascribeField', $result['field_id']);
                $record = $em->find('Datascribe\Entity\DatascribeRecord', $result['record_id']);
                $value = new DatascribeValue;
                $value->setField($field);
                $value->setRecord($record);
                $value->setIsInvalid(false);
                $value->setIsMissing(false);
                $value->setIsIllegible(false);
                $value->setText(null);
                $em->persist($value);
            }
            // Execute all updates and detach all objects after each batch.
            $em->flush();
            $em->clear();
        } while ($results);

        // Here we validate this dataset against the rules currently set in the
        // form builder. Once validated, we mark invalid values as invalid.
        $maxResults = 100;
        $offset = 0;
        $dql = '
            SELECT value
            FROM Datascribe\Entity\DatascribeValue value
            JOIN value.record record
            JOIN record.item item
            JOIN item.dataset dataset
            WHERE dataset.id = :datasetId';
        $query = $em->createQuery($dql)
            ->setParameter('datasetId', $dataset->getId())
            ->setMaxResults($maxResults);
        $dataTypes = $services->get('Datascribe\DataTypeManager');
        do {
            $result = $query->setFirstResult($offset)->getResult();
            foreach ($result as $value) {
                $field = $value->getField();
                $dataType = $dataTypes->get($field->getDataType());
                if (null === $value->getText()) {
                    // A null value is invalid only if the field is required.
                    $value->setIsInvalid($value->getField()->getIsRequired());
                } else {
                    $isValid = $dataType->valueTextIsValid($field->getData(), $value->getText());
                    $value->setIsInvalid(!$isValid);
                }
            }
            $offset = $offset + $maxResults;
            // Execute all updates and detach all objects after each batch.
            $em->flush();
            $em->clear();
        } while ($result);
    }
}
