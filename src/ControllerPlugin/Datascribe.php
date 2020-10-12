<?php
namespace Datascribe\ControllerPlugin;

use Datascribe\DatascribeDataType\Manager;
use Omeka\Api\Exception\NotFoundException;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\HelperPluginManager;

/**
 * Controller plugin used for DataScribe-specific functionality.
 */
class Datascribe extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @param ServiceLocatorInterface $services
     */
    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
    }

    /**
     * Get a DataScribe representation.
     *
     * Provides a single method to get a DataScribe project, dataset, item, or
     * record representation. Used primarily to ensure that the route is valid.
     *
     * @param int $projectId
     * @param int|null $datasetId
     * @param int|null $itemId
     * @return DatascribeProjectRepresentation|DatascribeDatasetRepresentation|DatascribeItemRepresentation|DatascribeRecordRepresentation
     */
    public function getRepresentation(int $projectId, ?int $datasetId = null, ?int $itemId = null, ?int $recordId = null)
    {
        $controller = $this->getController();
        if ($recordId) {
            try {
                $record = $controller->api()->read('datascribe_records', $recordId)->getContent();
            } catch (NotFoundException $e) {
                return false;
            }
            $item = $record->item();
            $dataset = $item->dataset();
            $project = $dataset->project();
            return (($itemId === $item->id()) && ($datasetId === $dataset->id()) && ($projectId === $project->id()))
                ? $record : false;
        }
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
