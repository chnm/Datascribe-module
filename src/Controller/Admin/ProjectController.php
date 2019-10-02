<?php
namespace Datascribe\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProjectController extends AbstractActionController
{
    public function browseAction()
    {
    }
    public function addAction()
    {
    }
    public function editAction()
    {
    }
    public function showAction()
    {
        return $this->redirect()->toRoute('admin/datascribe-dataset', ['action' => 'browse'], true);
    }
}
