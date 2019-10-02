<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Form\ProjectForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProjectController extends AbstractActionController
{
    public function browseAction()
    {
    }
    public function addAction()
    {
        $form = $this->getForm(ProjectForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $response = $this->api($form)->create('datascribe_projects', $formData);
                if ($response) {
                    $this->messenger()->addSuccess('DataScribe project successfully created.'); // @translate
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
    }
    public function showAction()
    {
        return $this->redirect()->toRoute('admin/datascribe-dataset', ['action' => 'browse'], true);
    }
}
