<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Form\RecordForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RecordController extends AbstractActionController
{
    public function browseAction()
    {
        $item = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id')
        );
        if (!$item) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $this->setBrowseDefaults('created');
        $query = array_merge(
            $this->params()->fromQuery(),
            ['datascribe_item_id' => $item->id()]
        );
        $response = $this->api()->search('datascribe_records', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $records = $response->getContent();

        $view = new ViewModel;
        $dataset = $item->dataset();
        $view->setVariable('project', $dataset->project());
        $view->setVariable('dataset', $dataset);
        $view->setVariable('item', $item);
        $view->setVariable('oItem', $item->item());
        $view->setVariable('records', $records);
        return $view;
    }

    public function addAction()
    {
        $item = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id')
        );
        if (!$item) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $dataset = $item->dataset();
        $form = $this->getForm(RecordForm::class, [
            'data_type_manager' => $this->datascribe()->getDataTypeManager(),
            'dataset' => $dataset,
        ]);
        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $formData['o-module-datascribe:item']['o:id'] = $item->id();
                $response = $this->api($form)->create('datascribe_records', $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Record successfully created.'); // @translate
                    return $this->redirect()->toUrl($response->getContent()->url());
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('form', $form);
        $view->setVariable('project', $dataset->project());
        $view->setVariable('dataset', $dataset);
        $view->setVariable('item', $item);
        $view->setVariable('oItem', $item->item());
        return $view;
    }

    public function editAction()
    {
    }

    public function showAction()
    {
    }
}
