<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Form\ProjectForm;
use Omeka\Form\ConfirmForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProjectController extends AbstractActionController
{
    public function addAction()
    {
        $form = $this->getForm(ProjectForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $formData['o:is_public'] = $this->params()->fromPost('o:is_public');
                $formData['o-module-datascribe:user'] = $this->params()->fromPost('o-module-datascribe:user');
                $response = $this->api($form)->create('datascribe_projects', $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Project successfully created.'); // @translate
                    return $this->redirect()->toUrl($response->getContent()->url());
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }

        $view = new ViewModel;
        $view->setVariable('form', $form);
        return $view;
    }

    public function editAction()
    {
        $project = $this->datascribe()->getRepresentation($this->params('project-id'));
        if (!$project) {
            return $this->redirect()->toRoute('admin/datascribe');
        }
        $form = $this->getForm(ProjectForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $formData['o:is_public'] = $this->params()->fromPost('o:is_public');
                $formData['o-module-datascribe:user'] = $this->params()->fromPost('o-module-datascribe:user');
                $response = $this->api($form)->update('datascribe_projects', $this->params('project-id'), $formData);
                if ($response) {
                    $this->messenger()->addSuccess('Project successfully edited.'); // @translate
                    return $this->redirect()->toUrl($response->getContent()->url());
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        } else {
            $data = $project->jsonSerialize();
            $form->setData($data);
        }

        $view = new ViewModel;
        $view->setVariable('form', $form);
        $view->setVariable('project', $project);
        return $view;
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $form = $this->getForm(ConfirmForm::class);
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $response = $this->api($form)->delete('datascribe_projects', $this->params('project-id'));
                if ($response) {
                    $this->messenger()->addSuccess('Project successfully deleted'); // @translate
                }
            } else {
                $this->messenger()->addFormErrors($form);
            }
        }
        return $this->redirect()->toRoute(null, ['action' => 'browse'], true);
    }

    public function browseAction()
    {
        $this->setBrowseDefaults('created');
        $response = $this->api()->search('datascribe_projects', $this->params()->fromQuery());
        $this->paginator($response->getTotalResults(), $this->params()->fromQuery('page'));
        $projects = $response->getContent();

        $view = new ViewModel;
        $view->setVariable('projects', $projects);
        return $view;
    }

    public function showDetailsAction()
    {
        $project = $this->datascribe()->getRepresentation($this->params('project-id'));
        if (!$project) {
            exit;
        }

        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setVariable('project', $project);
        return $view;
    }

    public function showAction()
    {
        return $this->redirect()->toRoute('admin/datascribe-dataset', ['action' => 'browse'], true);
    }
}
