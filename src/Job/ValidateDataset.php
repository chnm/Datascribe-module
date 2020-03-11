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
        $dataTypes = $services->get('Datascribe\DataTypeManager');

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
        do {
            $result = $query->setFirstResult($offset)->getResult();
            foreach ($result as $value) {
                $field = $value->getField();
                $dataType = $dataTypes->get($field->getDataType());
                if (null === $value->getText()) {
                    // Note that null values are always valid.
                    $value->setIsInvalid(false);
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
