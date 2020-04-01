<?php
namespace Datascribe\Job;

use Datascribe\Entity\DatascribeDataset;
use DateTime;
use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;

class ExportDataset extends AbstractJob
{
    /**
     * Export a dataset.
     */
    public function perform()
    {
        $services = $this->getServiceLocator();
        $em = $services->get('Omeka\EntityManager');
        $dispatcher = $services->get('Omeka\Job\Dispatcher');
        $strategy = $services->get('Omeka\Job\DispatchStrategy\Synchronous');
        $tempFileFactory = $services->get('Omeka\File\TempFileFactory');

        $datasetId = $this->getArg('datascribe_dataset_id');
        if (!is_numeric($datasetId)) {
            throw new Exception\RuntimeException('Missing datascribe_dataset_id');
        }
        $dataset = $em->find(DatascribeDataset::class, $datasetId);
        if (null === $dataset) {
            throw new Exception\RuntimeException('Cannot find dataset');
        }

        // First, validate the dataset synchronously.
        $dispatcher->dispatch(
            ValidateDataset::class,
            ['datascribe_dataset_id' => $dataset->getId()],
            $strategy
        );

        // Create the CSV file.
        $tempFile = $tempFileFactory->build();
        $fp = fopen($tempFile->getTempPath(), 'w');

        $maxResults = 100;
        $offset = 0;
        $dql = '
            SELECT record
            FROM Datascribe\Entity\DatascribeRecord record
            INNER JOIN record.item item
            INNER JOIN item.dataset dataset
            WHERE dataset.id = :datasetId
            AND item.isApproved = true
            ORDER BY item.id ASC, record.id ASC';
        $query = $em->createQuery($dql)
            ->setParameter('datasetId', $dataset->getId())
            ->setMaxResults($maxResults);
        do {
            $table = [];
            $result = $query->setFirstResult($offset)->getResult();
            foreach ($result as $record) {
                // Begin each row with the unique ID of the record so the user
                // can cross-reference the file with the dataset.
                $row = [$record->getId()];
                $values = $record->getValues();
                foreach ($dataset->getFields() as $field) {
                    // All values should exist since validation null-fills
                    // uncreated values, but we account for null values anyway.
                    $value = $values[$field->getId()] ?? null;
                    $valueText = null;
                    if ($value && (true !== $value->getIsInvalid())) {
                        // The value must exist and be valid.
                        $valueText = $value->getText();
                    }
                    $row[] = $valueText;
                }
                $table[] = $row;
            }
            // Add to the CSV file.
            foreach ($table as $row) {
                fputcsv($fp, $row);
            }
            $offset = $offset + $maxResults;
        } while ($result);

        fclose($fp);
        $tempFile->store('asset', 'csv');
        $tempFile->delete();

        $dataset->setExported(new DateTime('now'));
        $dataset->setExportedBy($this->job->getOwner());
        $dataset->setExportStorageId($tempFile->getStorageId());

        // Clear the entity manager to avoid a "A new entity was found" error.
        $em->clear();
        $em->merge($dataset);
        $em->flush();
    }
}
