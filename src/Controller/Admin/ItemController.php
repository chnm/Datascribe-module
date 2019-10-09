<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Form\DatasetSyncForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ItemController extends AbstractActionController
{
    public function browseAction()
    {
        try {
            $dataset = $this->api()->read('datascribe_datasets', $this->params('dataset-id'))->getContent();
        } catch (NotFoundException $e) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $this->setBrowseDefaults('created');
        $query = array_merge(
            ['datascribe_dataset_id' => $this->params('dataset-id')],
            $this->params()->fromQuery()
        );
        $response = $this->api()->search('datascribe_items', $this->params()->fromQuery());
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
