<?php
namespace Datascribe\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $userId = $this->identity()->getId();
        $myResponse = $this->api()->search(
            'datascribe_projects',
            [
                'sort_by' => 'name',
                'sort_order' => 'asc',
                'my_projects' => true,
            ]
        );
        $allProjects = $this->api()->search(
            'datascribe_projects',
            [
                'sort_by' => 'name',
                'sort_order' => 'asc',
            ]
        )->getContent();
        $myProjects = [];
        foreach ($myResponse->getContent() as $project) {
            $roles = [];
            if ($project->owner()->id() === $userId) {
                $roles[] = $this->translate('Owner');
            }
            $users = $project->users();
            $user = $users[$userId] ?? null;
            if ($user) {
                $roles[] = $this->translate($user->roleLabel());
            }
            $datasets = $this->api()->search(
                'datascribe_datasets',
                ['datascribe_project_id' => $project->id()]
            )->getContent();
            $myProjects[] = [
                'project' => $project,
                'roles' => $roles,
                'datasets' => $datasets,
            ];
        }

        $view = new ViewModel;
        $view->setVariable('allProjects', $allProjects);
        $view->setVariable('myProjects', $myProjects);
        return $view;
    }
}
