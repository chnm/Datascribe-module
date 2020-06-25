<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Form\ItemForm;
use Datascribe\Form\RecordBatchForm;
use Datascribe\Form\RecordForm;
use Datascribe\Form\RecordSearchForm;
use Omeka\Form\ConfirmForm;
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

        $oItem = $item->item();
        $dataset = $item->dataset();
        $project = $dataset->project();

        $this->setBrowseDefaults('position', 'asc');
        $query = array_merge(
            $this->params()->fromQuery(),
            ['datascribe_item_id' => $item->id()]
        );
        $response = $this->api()->search('datascribe_records', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $records = $response->getContent();

        $form = $this->getForm(ItemForm::class, [
            'project' => $project,
            'item' => $item,
        ]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $response = $this->api($form)->update('datascribe_items', $item->id(), $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Item successfully updated.'); // @translate
                    return $this->redirect()->toUrl($response->getContent()->url());
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $formDeleteSelected = $this->getForm(ConfirmForm::class);
        $formDeleteSelected->setAttribute('action', $this->url()->fromRoute(null, ['action' => 'batch-delete'], true));
        $formDeleteSelected->setButtonLabel('Confirm Delete'); // @translate
        $formDeleteSelected->setAttribute('id', 'confirm-delete-selected');

        $formDeleteAll = $this->getForm(ConfirmForm::class);
        $formDeleteAll->setAttribute('action', $this->url()->fromRoute(null, ['action' => 'batch-delete-all'], true));
        $formDeleteAll->setButtonLabel('Confirm Delete'); // @translate
        $formDeleteAll->setAttribute('id', 'confirm-delete-all');
        $formDeleteAll->get('submit')->setAttribute('disabled', true);

        $view = new ViewModel;
        $view->setVariable('form', $form);
        $view->setVariable('project', $project);
        $view->setVariable('dataset', $dataset);
        $view->setVariable('fields', $dataset->fields());
        $view->setVariable('item', $item);
        $view->setVariable('oItem', $oItem);
        $view->setVariable('records', $records);
        $view->setVariable('formDeleteSelected', $formDeleteSelected);
        $view->setVariable('formDeleteAll', $formDeleteAll);
        return $view;
    }

    public function showDetailsAction()
    {
        $record = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id'),
            $this->params('record-id')
        );
        if (!$record) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $item = $record->item();
        $dataset = $item->dataset();
        $project = $dataset->project();

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('project', $project);
        $view->setVariable('dataset', $dataset);
        $view->setVariable('item', $item);
        $view->setVariable('record', $record);
        return $view;
    }

    public function searchAction()
    {
        $item = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id')
        );
        if (!$item) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $form = $this->getForm(RecordSearchForm::class, ['parent' => $item]);
        $form->setAttribute('method', 'get');
        $form->setAttribute('action', $this->url()->fromRoute(null, ['action' => 'browse'], true));
        $form->setData($this->params()->fromQuery());

        $view = new ViewModel;
        $view->setVariable('item', $item);
        $view->setVariable('form', $form);
        $view->setVariable('query', $this->params()->fromQuery());
        return $view;
    }

    public function saveProgressAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $item = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id')
        );
        if (!$item) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $oItem = $item->item();
        $dataset = $item->dataset();
        $project = $dataset->project();

        $form = $this->getForm(RecordForm::class, [
            'item' => $item,
        ]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            $response = $this->getResponse();
            if ($form->isValid()) {
                $formData = $form->getData();
                $formData['o-module-datascribe:item']['o:id'] = $item->id();
                try {
                    $this->api(null, true)->create('datascribe_records', $formData);
                    $response->setStatusCode(200); // OK
                    $response->setContent(json_encode([]));
                } catch (\Omeka\Api\Exception\ValidationException $e) {
                    $errorStore = $e->getErrorStore();
                    $response->setStatusCode(422); // Unprocessable Entity
                    $response->setContent(json_encode($errorStore->getErrors()));
                }
            } else {
                $response->setStatusCode(422); // Unprocessable Entity
                $response->setContent(json_encode($form->getMessages()));
            }
            return $response;
        }
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

        $oItem = $item->item();
        $dataset = $item->dataset();
        $project = $dataset->project();

        $form = $this->getForm(RecordForm::class, [
            'item' => $item,
        ]);
        $form->setAttribute('id', 'record-form');
        $form->setAttribute('data-save-progress-url', $this->url()->fromRoute(null, ['action' => 'save-progress'], true));

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $formData['o-module-datascribe:item']['o:id'] = $item->id();
                $response = $this->api($form)->create('datascribe_records', $formData);
                if ($response) {
                    $record = $response->getContent();
                    $this->messenger()->addSuccess('Record successfully created.'); // @translate
                    if (isset($postData['submit-add-another'])) {
                        return $this->redirect()->toRoute(null, [], true);
                    } elseif (isset($postData['submit-save-progress'])) {
                        return $this->redirect()->toRoute('admin/datascribe-record-id', ['action' => 'edit', 'record-id' => $record->id()], true);
                    } else {
                        return $this->redirect()->toRoute('admin/datascribe-record', ['action' => 'browse'], true);
                    }
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $recordsPrevious = $this->api()->search('datascribe_records', [
            'datascribe_item_id' => $item->id(),
            'limit' => 10,
            'sort_by' => 'position',
            'sort_order' => 'desc',
        ])->getContent();

        $view = new ViewModel;
        $view->setVariable('action', 'add');
        $view->setVariable('form', $form);
        $view->setVariable('project', $project);
        $view->setVariable('dataset', $dataset);
        $view->setVariable('item', $item);
        $view->setVariable('oItem', $oItem);
        $view->setVariable('recordsPrevious', array_reverse($recordsPrevious));
        return $view;
    }

    public function editAction()
    {
        $record = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id'),
            $this->params('record-id')
        );
        if (!$record) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $item = $record->item();
        $oItem = $item->item();
        $dataset = $item->dataset();
        $project = $dataset->project();

        $form = $this->getForm(RecordForm::class, [
            'item' => $item,
            'record' => $record,
        ]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->setData($postData);
            if ($form->isValid()) {
                $formData = $form->getData();
                $response = $this->api($form)->update('datascribe_records', $record->id(), $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Record successfully edited.'); // @translate
                    if (isset($postData['submit-save-progress'])) {
                        return $this->redirect()->toRoute(null, [], true);
                    } else {
                        return $this->redirect()->toRoute('admin/datascribe-record', ['action' => 'browse'], true);
                    }
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $recordsPrevious = $this->api()->search('datascribe_records', [
            'datascribe_item_id' => $item->id(),
            'before_position' => $record->position(),
            'limit' => 10,
        ])->getContent();
        $recordsNext = $this->api()->search('datascribe_records', [
            'datascribe_item_id' => $item->id(),
            'after_position' => $record->position(),
            'limit' => 10,
        ])->getContent();

        $view = new ViewModel;
        $view->setVariable('action', 'edit');
        $view->setVariable('form', $form);
        $view->setVariable('project', $project);
        $view->setVariable('dataset', $dataset);
        $view->setVariable('item', $item);
        $view->setVariable('oItem', $oItem);
        $view->setVariable('record', $record);
        $view->setVariable('recordsPrevious', array_reverse($recordsPrevious));
        $view->setVariable('recordsNext', $recordsNext);
        return $view;
    }

    public function deleteAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $record = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id'),
            $this->params('record-id')
        );
        if (!$record) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $form = $this->getForm(ConfirmForm::class);
        $form->setData($this->getRequest()->getPost());
        if ($form->isValid()) {
            $response = $this->api($form)->delete('datascribe_records', $record->id());
            if ($response) {
                $this->messenger()->addSuccess('Record successfully deleted'); // @translate
            }
        } else {
            $this->messenger()->addFormErrors($form);
        }
        return $this->redirect()->toRoute('admin/datascribe-record', ['action' => 'browse'], true);
    }

    public function batchEditAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $item = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id')
        );
        if (!$item) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $dataset = $item->dataset();
        $project = $dataset->project();

        $recordIds = $this->params()->fromPost('record_ids', []);

        $records = [];
        foreach ($recordIds as $recordId) {
            $records[] = $this->api()->read('datascribe_records', $recordId)->getContent();
        }

        $form = $this->getForm(RecordBatchForm::class, ['dataset' => $item->dataset()]);

        if ($this->params()->fromPost('batch_edit')) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $this->api($form)->batchUpdate('datascribe_records', $recordIds, $formData);
                $this->messenger()->addSuccess('Records successfully edited.'); // @translate
                return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('dataset', $dataset);
        $view->setVariable('item', $item);
        $view->setVariable('records', $records);
        $view->setVariable('form', $form);
        return $view;
    }

    public function batchEditAllAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $item = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id')
        );
        if (!$item) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $dataset = $item->dataset();
        $project = $dataset->project();

        $query = json_decode($this->params()->fromPost('query', []), true);
        $query['datascribe_item_id'] = $item->id();
        unset(
            $query['submit'], $query['page'], $query['per_page'],
            $query['limit'], $query['offset'], $query['sort_by'],
            $query['sort_order']
        );
        $count = $this->api()->search('datascribe_records', array_merge($query, ['limit' => 0]))->getTotalResults();

        $form = $this->getForm(RecordBatchForm::class, ['dataset' => $item->dataset()]);

        if ($this->params()->fromPost('batch_edit')) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $job = $this->jobDispatcher()->dispatch('Omeka\Job\BatchUpdate', [
                    'resource' => 'datascribe_records',
                    'query' => $query,
                    'data' => $formData,
                ]);
                $this->messenger()->addSuccess('Editing records. This may take a while.'); // @translate
                return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('dataset', $dataset);
        $view->setVariable('item', $item);
        $view->setVariable('count', $count);
        $view->setVariable('query', $query);
        $view->setVariable('form', $form);
        return $view;
    }

    public function batchDeleteAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $item = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id')
        );
        if (!$item) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $recordIds = $this->params()->fromPost('record_ids', []);
        if (!$recordIds) {
            $this->messenger()->addError('You must select at least one record to batch delete.'); // @translate
            return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
        }

        $form = $this->getForm(ConfirmForm::class);
        $form->setData($this->getRequest()->getPost());
        if ($form->isValid()) {
            $response = $this->api($form)->batchDelete('datascribe_records', $recordIds, [], ['continueOnError' => true]);
            if ($response) {
                $this->messenger()->addSuccess('Records successfully deleted'); // @translate
            }
        } else {
            $this->messenger()->addFormErrors($form);
        }
        return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
    }

    public function batchDeleteAllAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $item = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id')
        );
        if (!$item) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        // Derive the query, removing limiting and sorting params.
        $query = json_decode($this->params()->fromPost('query', []), true);
        $query['datascribe_item_id'] = $item->id();
        unset(
            $query['submit'], $query['page'], $query['per_page'],
            $query['limit'], $query['offset'], $query['sort_by'],
            $query['sort_order']
        );

        $form = $this->getForm(ConfirmForm::class);
        $form->setData($this->getRequest()->getPost());
        if ($form->isValid()) {
            $job = $this->jobDispatcher()->dispatch('Omeka\Job\BatchDelete', [
                'resource' => 'datascribe_records',
                'query' => $query,
            ]);
            $this->messenger()->addSuccess('Deleting records. This may take a while.'); // @translate
        } else {
            $this->messenger()->addFormErrors($form);
        }
        return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
    }

    public function showAction()
    {
        $record = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id'),
            $this->params('item-id'),
            $this->params('record-id')
        );
        if (!$record) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $item = $record->item();
        $oItem = $item->item();
        $dataset = $item->dataset();
        $project = $dataset->project();

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('dataset', $dataset);
        $view->setVariable('item', $item);
        $view->setVariable('oItem', $oItem);
        $view->setVariable('record', $record);
        return $view;
    }
}
