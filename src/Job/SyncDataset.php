<?php
namespace Datascribe\Job;

use Datascribe\Entity\DatascribeDataset;
use Datascribe\Entity\DatascribeItem;
use DateTime;
use Omeka\Entity\Item;
use Omeka\Entity\ItemSet;
use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;

class SyncDataset extends AbstractJob
{
    /**
     * Sync a dataset with its item set.
     */
    public function perform()
    {
        if (!is_numeric($this->getArg('datascribe_dataset_id'))) {
            throw new Exception\RuntimeException('Missing datascribe_dataset_id');
        }
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $dataset = $em->find(DatascribeDataset::class, $this->getArg('datascribe_dataset_id'));
        if (null === $dataset) {
            throw new Exception\RuntimeException('Cannot find dataset');
        }
        if (null === $dataset->getItemSet()) {
            throw new Exception\RuntimeException('Cannot sync dataset without an item set');
        }

        $dataset->setSynced(new DateTime('now'));
        $dataset->setSyncedBy($this->job->getOwner());

        $dsItems = $this->getDatasetItemIds($dataset);
        $oItems = $this->getItemSetItemIds($dataset->getItemSet());

        // Calculate which items to delete and which to create.
        $toDelete = array_diff($dsItems, $oItems);
        $toCreate = array_diff($oItems, $dsItems);

        // Create new DataScribe items.
        foreach ($toCreate as $oItemId) {
            $dsItem = new DatascribeItem;
            $dsItem->setDataset($dataset);
            $dsItem->setItem($em->getReference(Item::class, $oItemId));
            $dsItem->setSynced(new DateTime('now'));
            $dsItem->setSyncedBy($this->job->getOwner());
            $em->persist($dsItem);
        }

        // Delete removed DataScribe items.
        $query = $em->createQuery('
            DELETE FROM Datascribe\Entity\DatascribeItem dsitem
            WHERE dsitem.id IN (:dsitem_ids)
        ')->setParameter('dsitem_ids', array_keys($toDelete));
        $query->execute();

        $em->flush();
    }

    /**
     * Get the IDs of all items in the DataScribe dataset.
     *
     * @param DatascribeDataset $dataset
     * @return array
     */
    public function getDatasetItemIds(DatascribeDataset $dataset)
    {
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $query = $em->createQuery('
            SELECT dsitem.id dsitem_id, oitem.id oitem_id
            FROM Datascribe\Entity\DatascribeItem dsitem
            JOIN dsitem.item oitem
            JOIN dsitem.dataset dsdataset
            WHERE dsdataset.id = :dsdataset_id'
        );
        // Execute the statement directly to optimize memory usage.
        $conn = $em->getConnection();
        $stmt = $conn->prepare($query->getSQL());
        $stmt->bindValue(1, $dataset->getId());
        $stmt->execute();
        $results = [];
        foreach ($stmt as $row) {
            $results[$row['id_0']] = $row['id_1'];
        }
        return $results;
    }

    /**
     * Get the IDs of all items in the item set.
     *
     * @param ItemSet $itemSet
     * @return array
     */
    public function getItemSetItemIds(ItemSet $itemSet)
    {
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $query = $em->createQuery('
            SELECT item.id
            FROM Omeka\Entity\Item item
            JOIN item.itemSets item_set
            WHERE item_set.id = :item_set_id'
        );
        // Execute the statement directly to optimize memory usage.
        $conn = $em->getConnection();
        $stmt = $conn->prepare($query->getSQL());
        $stmt->bindValue(1, $itemSet->getId());
        $stmt->execute();
        $results = [];
        foreach ($stmt as $row) {
            $results[] = $row['id_0'];
        }
        return $results;
    }
}
