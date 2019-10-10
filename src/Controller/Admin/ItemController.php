<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Form\DatasetSyncForm;
use Omeka\Stdlib\Message;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ItemController extends AbstractActionController
{
    public function browseAction()
    {
        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $this->setBrowseDefaults('created');
        $query = array_merge(
            $this->params()->fromQuery(),
            ['datascribe_dataset_id' => $dataset->id()]
        );
        $response = $this->api()->search('datascribe_items', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $items = $response->getContent();

        if (!$dataset->itemSet()) {
            $message = new Message(
                'This dataset has no item set. %s', // @translate
                sprintf(
                    '<a href="%s">%s</a>',
                    htmlspecialchars($dataset->adminUrl('edit')),
                    $this->translate('Set an item set here.')
                ));
            $message->setEscapeHtml(false);
            $this->messenger()->addError($message);
        }

        $view = new ViewModel;
        $view->setVariable('project', $dataset->project());
        $view->setVariable('dataset', $dataset);
        $view->setVariable('items', $items);
        $view->setVariable('syncForm', $this->getForm(DatasetSyncForm::class, ['dataset' => $dataset]));
        return $view;
    }

    public function showDetailsAction()
    {
    }

    public function showAction()
    {
    }
}
