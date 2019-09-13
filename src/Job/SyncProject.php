<?php
namespace Datascribe\Job;

use Datascribe\Entity\DatascribeProject;
use Datascribe\Entity\DatascribeItem;
use DateTime;
use Omeka\Entity\Item;
use Omeka\Entity\ItemSet;
use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;

class SyncProject extends AbstractJob
{
    /**
     * Sync a project with its item set.
     */
    public function perform()
    {
        if (!is_numeric($this->getArg('project_id'))) {
            throw new Exception\RuntimeException('Missing project_id');
        }
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $project = $em->find(DatascribeProject::class, $this->getArg('project_id'));
        if (null === $project) {
            throw new Exception\RuntimeException('Cannot find project');
        }
        if (null === $project->getItemSet()) {
            throw new Exception\RuntimeException('Cannot sync project without an item set');
        }

        $dsItems = $this->getProjectItemIds($project);
        $oItems = $this->getItemSetItemIds($project->getItemSet());

        // Calculate which items to delete and which to create.
        $toDelete = array_diff($dsItems, $oItems);
        $toCreate = array_diff($oItems, $dsItems);

        // Create new DataScribe items.
        foreach ($toCreate as $oItemId) {
            $dsItem = new DatascribeItem;
            $dsItem->setProject($project);
            $dsItem->setItem($em->getReference(Item::class, $oItemId));
            $em->persist($dsItem);
        }

        // Delete removed DataScribe items.
        $query = $em->createQuery('
            DELETE FROM Datascribe\Entity\DatascribeItem dsitem
            WHERE dsitem.id IN (:dsitem_ids)
        ')->setParameter('dsitem_ids', array_keys($toDelete));
        $query->execute();

        $project->setSynced(new DateTime('now'));
        $em->flush();
    }

    /**
     * Get the IDs of all items in the DataScribe project.
     *
     * @param DatascribeProject $project
     * @return array
     */
    public function getProjectItemIds(DatascribeProject $project)
    {
        $em = $this->getServiceLocator()->get('Omeka\EntityManager');
        $query = $em->createQuery('
            SELECT dsitem.id dsitem_id, oitem.id oitem_id
            FROM Datascribe\Entity\DatascribeItem dsitem
            JOIN dsitem.item oitem
            JOIN dsitem.project dsproject
            WHERE dsproject.id = :dsproject_id'
        );
        // Execute the statement directly to optimize memory usage.
        $conn = $em->getConnection();
        $stmt = $conn->prepare($query->getSQL());
        $stmt->bindValue(1, $project->getId());
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
