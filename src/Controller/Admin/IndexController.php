<?php
namespace Datascribe\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $userId = $this->identity()->getId();
        $myResponse = $this->api()->search(
            'datascribe_projects',
            [
                'sort_by' => 'modified',
                'sort_order' => 'desc',
                'limit' => 10,
            ]
        );
        $recentProjects = $this->api()->search(
            'datascribe_projects',
            [
                'sort_by' => 'name',
                'sort_order' => 'asc',
                'my_projects' => false,
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
        $view->setVariable('recentProjects', $recentProjects);
        $view->setVariable('myProjects', $myProjects);
        return $view;
    }
}
