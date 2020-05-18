<?php
namespace Datascribe\ViewHelper;

use Datascribe\Api\Representation\DatascribeDatasetRepresentation;
use Datascribe\Api\Representation\DatascribeRecordRepresentation;
use Datascribe\Entity\DatascribeField;
use Datascribe\Form\DatasetForm;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
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
            $bc[] = $view->hyperlink($view->translate($this->bcRouteMap[$bcRoute]['text']), $view->url($bcRoute, $params));
        }
        $bc[] = $view->translate($this->bcRouteMap[$routeName]['text']);
        return sprintf('<div class="breadcrumbs">%s</div>', implode('<div class="separator"></div>', $bc));
    }
}
