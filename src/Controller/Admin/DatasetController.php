<?php
namespace Datascribe\Controller\Admin;

use Datascribe\Form\DatasetForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DatasetController extends AbstractActionController
{
    public function addAction()
    {
        try {
            $project = $this->api()->read('datascribe_projects', $this->params('project-id'))->getContent();
        } catch (NotFoundException $e) {
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
                $response = $this->api($form)->create('datascribe_datasets', $formData);
                if ($response) {
                    $this->messenger()->addSuccess('DataScribe dataset successfully created.'); // @translate
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

    public function deleteAction()
    {
    }

    public function browseAction()
    {
    }

    public function showDetailsAction()
    {
    }

    public function showAction()
    {
        return $this->redirect()->toRoute('admin/datascribe-item', ['action' => 'browse'], true);
    }
}
