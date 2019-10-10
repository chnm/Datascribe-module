<?php
namespace Datascribe\ControllerPlugin;

use Omeka\Api\Exception\NotFoundException;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Controller plugin used for DataScribe-specific functionality.
 */
class Datascribe extends AbstractPlugin
{
    /**
     * Get a Scripto representation.
     *
     * Provides a single method to get a DataScribe project, dataset, or item
     * representation. Used primarily to ensure that the route is valid.
     *
     * @param int $projectId
     * @param int|null $datasetId
     * @param int|null $itemId
     * @return DatascribeProjectRepresentation|DatascribeDatasetRepresentation|DatascribeItemRepresentation
     */
    public function getRepresentation(int $projectId, ?int $datasetId = null, ?int $itemId = null)
    {
        $controller = $this->getController();
        if ($itemId) {
            try {
                $item = $controller->api()->read('datascribe_items', $itemId)->getContent();
            } catch (NotFoundException $e) {
                return false;
            }
            $dataset = $item->dataset();
            $project = $dataset->project();
            return (($datasetId === $dataset->id()) && ($projectId === $project->id()))
                ? $item : false;
        }
        if ($datasetId) {
            try {
                $dataset = $controller->api()->read('datascribe_datasets', $datasetId)->getContent();
            } catch (NotFoundException $e) {
                return false;
            }
            $project = $dataset->project();
            return ($projectId === $project->id())
                ? $dataset : false;
        }
        try {
            $project = $controller->api()->read('datascribe_projects', $projectId)->getContent();
        } catch (NotFoundException $e) {
            return false;
        }
        return $project;
    }
}
