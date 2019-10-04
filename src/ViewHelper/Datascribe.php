<?php
namespace Datascribe\ViewHelper;

use Zend\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * View helper used to render DataScribe template elements.
 */
class Datascribe extends AbstractHelper
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @param RouteMatch $routeMatch
     */
    public function __construct(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
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
        $routeName = $this->routeMatch->getMatchedRouteName();
        if (!isset($this->bcRouteMap[$routeName])) {
            return '';
        }
        foreach ($this->bcRouteMap[$routeName]['breadcrumbs'] as $bcRoute) {
            $params = [];
            foreach ($this->bcRouteMap[$bcRoute]['params'] as $bcParam) {
                $params[$bcParam] = $this->routeMatch->getParam($bcParam);
            }
            $bc[] = $view->hyperlink($this->bcRouteMap[$bcRoute]['text'], $view->url($bcRoute, $params));
        }
        $bc[] = $view->translate($this->bcRouteMap[$routeName]['text']);
        return sprintf('<div class="breadcrumbs">%s</div>', implode('<div class="separator"></div>', $bc));
    }
}
