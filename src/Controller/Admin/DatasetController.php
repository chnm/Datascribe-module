<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Entity\DatascribeDataset;
use Datascribe\Entity\DatascribeProject;
use Datascribe\Form\DatasetForm;
use Datascribe\Form\DatasetExportForm;
use Datascribe\Form\DatasetMoveForm;
use Datascribe\Form\DatasetSyncForm;
use Datascribe\Form\DatasetValidateForm;
use Datascribe\Job\ExportDataset;
use Datascribe\Job\SyncDataset;
use Datascribe\Job\ValidateDataset;
use Doctrine\ORM\EntityManager;
use Omeka\Form\ConfirmForm;
use Omeka\Stdlib\Message;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class DatasetController extends AbstractActionController
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addAction()
    {
        $project = $this->datascribe()->getRepresentation($this->params('project-id'));
        if (!$project) {
            return $this->redirect()->toRoute('admin/datascribe');
        }
        $form = $this->getForm(DatasetForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $formData['o-module-datascribe:project']['o:id'] = $project->id();
                $formData['o:item_set'] = ['o:id' => $formData['o:item_set']];
                $formData['o:is_public'] = $this->params()->fromPost('o:is_public');
                // Import form if file was uploaded.
                $files = $this->getRequest()->getFiles()->toArray();
                if ($files['import_form']['tmp_name']) {
                    $importForm = json_decode(file_get_contents($files['import_form']['tmp_name']), true);
                    $formData['o-module-datascribe:field'] = $importForm;
                }
                $response = $this->api($form)->create('datascribe_datasets', $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Dataset successfully created.'); // @translate
                    return $this->redirect()->toUrl($response->getContent()->url());
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('form', $form);
        $view->setVariable('project', $project);
        return $view;
    }

    public function editAction()
    {
        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
            return $this->redirect()->toRoute('admin/datascribe');
        }
        $form = $this->getForm(DatasetForm::class, [
            'dataset' => $dataset,
        ]);

        if ($this->getRequest()->isPost()) {
            $postData = $this->params()->fromPost();
            $form->removeDeletedFields($postData)->setData($postData);
            if ($form->isValid()) {
                // Note that the form cannot validate new fields. Instead we
                // rely on browser and API validation, and we pass POST data to
                // the API instead of parsed form data.
                $postData['o:item_set'] = ['o:id' => $postData['o:item_set']];
                $response = $this->api($form)->update('datascribe_datasets', $this->params('dataset-id'), $postData);
                if ($response) {
                    $this->messenger()->addSuccess('Dataset successfully edited.'); // @translate
                    if (isset($postData['submit-save-progress'])) {
                        return $this->redirect()->toRoute(null, [], ['fragment' => 'form-builder'], true);
                    } else {
                        return $this->redirect()->toUrl($response->getContent()->url());
                    }
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        } else {
            $data = $dataset->jsonSerialize();
            $data['o:item_set'] = $data['o:item_set'] ? $data['o:item_set']->id() : null;
            $form->setData($data);
        }

        $view = new ViewModel;
        $view->setVariable('form', $form);
        $view->setVariable('project', $dataset->project());
        $view->setVariable('dataset', $dataset);
        return $view;
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(ConfirmForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $response = $this->api($form)->delete('datascribe_datasets', $this->params('dataset-id'));
                if ($response) {
                    $this->messenger()->addSuccess('Dataset successfully deleted'); // @translate
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }
        return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
    }

    public function browseAction()
    {
        $project = $this->datascribe()->getRepresentation($this->params('project-id'));
        if (!$project) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $this->setBrowseDefaults('created');
        $query = array_merge(
            $this->params()->fromQuery(),
            ['datascribe_project_id' => $project->id()]
        );
        $response = $this->api()->search('datascribe_datasets', $query);
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $datasets = $response->getContent();

        $view = new ViewModel;
        $view->setVariable('project', $project);
        $view->setVariable('datasets', $datasets);
        return $view;
    }

    public function showDetailsAction()
    {
        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('project', $dataset->project());
        $view->setVariable('dataset', $dataset);
        return $view;
    }

    public function showAction()
    {
        return $this->redirect()->toRoute('admin/datascribe-item', ['action' => 'browse'], true);
    }

    public function syncAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(DatasetSyncForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $job = $this->jobDispatcher()->dispatch(
                    SyncDataset::class,
                    ['datascribe_dataset_id' => $this->params('dataset-id')]
                );
                $message = new Message(
                    'Syncing dataset. This may take a while. %s', // @translate
                    sprintf(
                        '<a href="%s">%s</a>',
                        htmlspecialchars($this->url()->fromRoute('admin/id', ['controller' => 'job', 'id' => $job->getId()])),
                        $this->translate('See this job for sync progress.')
                    ));
                $message->setEscapeHtml(false);
                $this->messenger()->addSuccess($message);
            }
        }
        return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
    }

    public function validateAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(DatasetValidateForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $job = $this->jobDispatcher()->dispatch(
                    ValidateDataset::class,
                    ['datascribe_dataset_id' => $this->params('dataset-id')]
                );
                $message = new Message(
                    'Validating dataset. This may take a while. %s', // @translate
                    sprintf(
                        '<a href="%s">%s</a>',
                        htmlspecialchars($this->url()->fromRoute('admin/id', ['controller' => 'job', 'id' => $job->getId()])),
                        $this->translate('See this job for validate progress.')
                    ));
                $message->setEscapeHtml(false);
                $this->messenger()->addSuccess($message);
            }
        }
        return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
    }

    public function exportAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(DatasetExportForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $job = $this->jobDispatcher()->dispatch(
                    ExportDataset::class,
                    ['datascribe_dataset_id' => $this->params('dataset-id')]
                );
                $message = new Message(
                    'Exporting dataset. This may take a while. %s', // @translate
                    sprintf(
                        '<a href="%s">%s</a>',
                        htmlspecialchars($this->url()->fromRoute('admin/id', ['controller' => 'job', 'id' => $job->getId()])),
                        $this->translate('See this job for export progress.')
                    ));
                $message->setEscapeHtml(false);
                $this->messenger()->addSuccess($message);
            }
        }
        return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
    }

    public function moveAction()
    {
        if ($this->getRequest()->isPost()) {
            $dataset = $this->datascribe()->getRepresentation(
                $this->params('project-id'),
                $this->params('dataset-id')
            );
            if (!$dataset) {
                return $this->redirect()->toRoute('admin/datascribe');
            }
            $form = $this->getForm(DatasetMoveForm::class, ['dataset' => $dataset]);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $projectEntity = $this->entityManager->find(DatascribeProject::class, $formData['project_id']);
                $datasetEntity = $this->entityManager->find(DatascribeDataset::class, $dataset->id());
                $datasetEntity->setProject($projectEntity);
                $this->entityManager->flush();
                $this->messenger()->addSuccess('Dataset successfully moved'); // @translate
                return $this->redirect()->toUrl($dataset->url());
            }
        }
        return $this->redirect()->toUrl($this->getRequest()->getHeader('Referer')->getUri());
    }

    public function exportFormAction()
    {
        $dataset = $this->datascribe()->getRepresentation(
            $this->params('project-id'),
            $this->params('dataset-id')
        );
        if (!$dataset) {
            return $this->redirect()->toRoute('admin/datascribe');
        }

        $response = $this->getResponse();
        $fields = [];
        foreach ($dataset->fields() as $field) {
            $fields[] = [
                'data_type' => $field->dataType(),
                'name' => $field->name(),
                // DatascribeDatasetAdapter::validateRequest() requires description to be set.
                'description' => $field->description() ?? '',
                'is_primary' => $field->isPrimary(),
                'is_required' => $field->isRequired(),
                'data' => $field->data(),
            ];
        }
        $response->setContent(json_encode($fields, JSON_PRETTY_PRINT));

        $headers = $response->getHeaders();
        $headers->addHeaders([
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="form_export.json"',
        ]);

        return $response;
    }
}
