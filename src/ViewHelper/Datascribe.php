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

    /**
     * Get the item review status select markup.
     *
     * @param string $name
     * @param string $value
     * @param string $label
     * @return string
     */
    public function itemReviewStatusSelect($name, $value, $label)
    {
        $view = $this->getView();
        $valueOptions = [
            'new' => $view->translate('New'),
            'in_progress' => $view->translate('In progress'),
            'need_review' => $view->translate('Need review'),
            'not_approved' => $view->translate('Not approved'),
            'approved' => $view->translate('Approved'),
        ];
        $select = (new Select($name))
            ->setLabel($label)
            ->setValueOptions($valueOptions)
            ->setEmptyOption($view->translate('Select status…'))
            ->setValue($value);
        return $view->formRow($select);
    }

    /**
     * Get the item locked status select markup.
     *
     * @param string $name
     * @param string $value
     * @param string $label
     * @return string
     */
    public function itemLockedStatusSelect($name, $value, $label)
    {
        $view = $this->getView();
        $valueOptions = [
            'unlocked' => $view->translate('Unlocked'),
            'locked' => $view->translate('Locked'),
        ];
        $select = (new Select($name))
            ->setLabel($label)
            ->setValueOptions($valueOptions)
            ->setEmptyOption($view->translate('Select status…'))
            ->setValue($value);
        return $view->formRow($select);
    }

    /**
     * Get the item locked by select markup.
     *
     * @param int $projectId
     * @param string $name
     * @param string $value
     * @param string $label
     * @return string
     */
    public function itemLockedBySelect($projectId, $name, $value, $label)
    {
        return $this->itemUserSelect('lockedBy', $projectId, $name, $value, $label);
    }

    /**
     * Get the item submitted by select markup.
     *
     * @param int $projectId
     * @param string $name
     * @param string $value
     * @param string $label
     * @return string
     */
    public function itemSubmittedBySelect($projectId, $name, $value, $label)
    {
        return $this->itemUserSelect('submittedBy', $projectId, $name, $value, $label);
    }

    /**
     * Get the item reviewed by select markup.
     *
     * @param int $projectId
     * @param string $name
     * @param string $value
     * @param string $label
     * @return string
     */
    public function itemReviewedBySelect($projectId, $name, $value, $label)
    {
        return $this->itemUserSelect('reviewedBy', $projectId, $name, $value, $label);
    }

    /**
     * Get the item user select markup.
     *
     * This will only get users who are set in the $byColumn.
     *
     * @param string $byColumn
     * @param int $projectId
     * @param string $name
     * @param string $value
     * @param string $label
     * @return string
     */
    protected function itemUserSelect($byColumn, $projectId, $name, $value, $label)
    {
        $view = $this->getView();
        $valueOptions = [];
        // Only query valid user columns.
        if (in_array($byColumn, ['lockedBy', 'submittedBy', 'reviewedBy'])) {
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
            foreach ($users as $user) {
                $valueOptions[$user->getId()] = sprintf('%s (%s)', $user->getName(), $user->getEmail());
            }
        }
        $select = (new Select($name))
            ->setLabel($label)
            ->setValueOptions($valueOptions)
            ->setEmptyOption($view->translate('Select user…'))
            ->setValue($value);
        return $view->formRow($select);
    }
}
