<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Form\DatasetExportForm;
use Datascribe\Form\DatasetMoveForm;
use Datascribe\Form\DatasetSyncForm;
use Datascribe\Form\DatasetValidateForm;
use Datascribe\Form\RecordBatchForm;
use Datascribe\Form\RecordSearchForm;
use Omeka\Form\ConfirmForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class DatasetRecordController extends AbstractActionController
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

        $this->setBrowseDefaults('position', 'asc');
        $query = array_merge(
            $this->params()->fromQuery(),
            ['datascribe_dataset_id' => $dataset->id()]
        );
        $response = $this->api()->search('datascribe_records', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $records = $response->getContent();

        $formDeleteSelected = $this->getForm(ConfirmForm::class);
        $formDeleteSelected->setAttribute('action', $this->url()->fromRoute(null, ['action' => 'batch-delete'], true));
        $formDeleteSelected->setButtonLabel('Confirm Delete'); // @translate
        $formDeleteSelected->setAttribute('id', 'confirm-delete-selected');

        $formDeleteAll = $this->getForm(ConfirmForm::class);
        $formDeleteAll->setAttribute('action', $this->url()->fromRoute(null, ['action' => 'batch-delete-all'], true));
        $formDeleteAll->setButtonLabel('Confirm Delete'); // @translate
        $formDeleteAll->setAttribute('id', 'confirm-delete-all');
        $formDeleteAll->get('submit')->setAttribute('disabled', true);

        $formSync = $this->getForm(DatasetSyncForm::class, ['dataset' => $dataset]);
        $formSync->setAttribute('action', $this->url()->fromRoute('admin/datascribe-dataset-id', ['action' => 'sync'], true));

        $formValidate = $this->getForm(DatasetValidateForm::class, ['dataset' => $dataset]);
        $formValidate->setAttribute('action', $this->url()->fromRoute('admin/datascribe-dataset-id', ['action' => 'validate'], true));

        $formExport = $this->getForm(DatasetExportForm::class, ['dataset' => $dataset]);
        $formExport->setAttribute('action', $this->url()->fromRoute('admin/datascribe-dataset-id', ['action' => 'export'], true));

        $formMove = $this->getForm(DatasetMoveForm::class, ['dataset' => $dataset]);
        $formMove->setAttribute('action', $this->url()->fromRoute('admin/datascribe-dataset-id', ['action' => 'move'], true));

        $view = new ViewModel;
        $view->setVariable('project', $dataset->project());
        $view->setVariable('dataset', $dataset);
        $view->setVariable('fields', $dataset->fields());
        $view->setVariable('records', $records);
        $view->setVariable('formDeleteSelected', $formDeleteSelected);
        $view->setVariable('formDeleteAll', $formDeleteAll);
        $view->setVariable('formSync', $formSync);
        $view->setVariable('formValidate', $formValidate);
        $view->setVariable('formExport', $formExport);
        $view->setVariable('formMove', $formMove);
        return $view;
    }

    public function searchAction()
    {
        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $form = $this->getForm(RecordSearchForm::class, ['parent' => $dataset]);
        $form->setAttribute('method', 'get');
        $form->setAttribute('action', $this->url()->fromRoute(null, ['action' => 'browse'], true));
        $form->setData($this->params()->fromQuery());

        $view = new ViewModel;
        $view->setVariable('dataset', $dataset);
        $view->setVariable('form', $form);
        $view->setVariable('query', $this->params()->fromQuery());
        return $view;
    }

    public function batchEditAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $project = $dataset->project();

        $recordIds = $this->params()->fromPost('record_ids', []);

        $records = [];
        foreach ($recordIds as $recordId) {
            $records[] = $this->api()->read('datascribe_records', $recordId)->getContent();
        }

        $form = $this->getForm(RecordBatchForm::class, ['dataset' => $dataset]);

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
        $view->setVariable('records', $records);
        $view->setVariable('form', $form);
        return $view;
    }

    public function batchEditAllAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $project = $dataset->project();

        $query = json_decode($this->params()->fromPost('query', []), true);
        $query['datascribe_dataset_id'] = $dataset->id();
        unset(
            $query['submit'], $query['page'], $query['per_page'],
            $query['limit'], $query['offset'], $query['sort_by'],
            $query['sort_order']
        );
        $count = $this->api()->search('datascribe_records', array_merge($query, ['limit' => 0]))->getTotalResults();

        $form = $this->getForm(RecordBatchForm::class, ['dataset' => $dataset]);

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

        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
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

        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        // Derive the query, removing limiting and sorting params.
        $query = json_decode($this->params()->fromPost('query', []), true);
        $query['datascribe_dataset_id'] = $dataset->id();
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
}
