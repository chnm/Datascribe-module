<?php
namespace Datascribe\ViewHelper;

use Zend\Form\Element\Select;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * View helper used to render DataScribe template elements.
 */
class Datascribe extends AbstractHelper
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @var array
     */
    protected $bcRouteMap;

    /**
     * @param ServiceLocatorInterface $services
     */
    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
        $this->bcRouteMap = include('breadcrumbs_route_map.php');
    }

    /**
     * Render DataScribe interface breadcrumbs.
     *
     * @return string
     */
    public function breadcrumbs() : string
    {
        $bc = [];
        $view = $this->getView();
        $routeMatch =  $this->services->get('Application')->getMvcEvent()->getRouteMatch();
        $routeName = $routeMatch->getMatchedRouteName();
        if (!isset($this->bcRouteMap[$routeName])) {
            return '';
        }
        foreach ($this->bcRouteMap[$routeName]['breadcrumbs'] as $bcRoute) {
            $params = [];
            foreach ($this->bcRouteMap[$bcRoute]['params'] as $bcParam) {
                $params[$bcParam] = $routeMatch->getParam($bcParam);
            }
            $bc[] = $view->hyperlink($this->bcRouteMap[$bcRoute]['text'], $view->url($bcRoute, $params));
        }
        $bc[] = $view->translate($this->bcRouteMap[$routeName]['text']);
        return sprintf('<div class="breadcrumbs">%s</div>', implode('<div class="separator"></div>', $bc));
    }

    public function itemReviewStatusSelect($name, $value, $label)
    {
        $view = $this->getView();
        $valueOptions = [
            'not_submitted' => $view->translate('Not submitted'),
            'new' => $view->translate('New - not submitted and have no records'),
            'in_progress' => $view->translate('In progress - not submitted and have records'),
            'need_review' => $view->translate('Need review'),
            'submitted' => $view->translate('Submitted - need review'),
            'resubmitted' => $view->translate('Re-submitted - need re-review'),
            'not_approved' => $view->translate('Not approved - reviewed and need work'),
            'approved' => $view->translate('Approved'),
        ];
        $select = (new Select($name))
            ->setLabel($label)
            ->setValueOptions($valueOptions)
            ->setEmptyOption($view->translate('Select review status…'))
            ->setValue($value);
        return $view->formRow($select);
    }

    public function itemLockedBySelect($projectId, $name, $value, $label)
    {
        return $this->itemUserSelect('lockedBy', $projectId, $name, $value, $label);
    }

    public function itemSubmittedBySelect($projectId, $name, $value, $label)
    {
        return $this->itemUserSelect('submittedBy', $projectId, $name, $value, $label);
    }

    public function itemReviewedBySelect($projectId, $name, $value, $label)
    {
        return $this->itemUserSelect('reviewedBy', $projectId, $name, $value, $label);
    }

    protected function itemUserSelect($byColumn, $projectId, $name, $value, $label)
    {
        $view = $this->getView();
        $em = $this->services->get('Omeka\EntityManager');
        $dql = "
            SELECT u
            FROM Omeka\Entity\User u
            JOIN Datascribe\Entity\DatascribeItem i WITH i.$byColumn = u
            JOIN i.dataset d
            JOIN d.project p
            WHERE p = :projectId";
        $query = $em->createQuery($dql);
        $query->setParameter('projectId', $projectId);
        $users = $query->getResult();
        usort($users, function ($userA, $userB) {
            return strcmp($userA->getName(), $userB->getName());
        });
        $valueOptions = [];
        foreach ($users as $user) {
            $valueOptions[$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
        }
        $select = (new Select($name))
            ->setLabel($label)
            ->setValueOptions($valueOptions)
            ->setEmptyOption($view->translate('Select user…'))
            ->setValue($value);
        return $view->formRow($select);
    }
}
