<?php
namespace Datascribe\ViewHelper;

use Datascribe\Api\Representation\DatascribeDatasetRepresentation;
use Datascribe\Api\Representation\DatascribeRecordRepresentation;
use Datascribe\DatascribeDataType\Fallback;
use Datascribe\Entity\DatascribeField;
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

    /**
     * Get all datasets keyed by the data set name.
     *
     * @return array
     */
    public function dataTypes() : array
    {
        $manager = $this->services->get('Datascribe\DataTypeManager');
        $dataTypes = [];
        $dataTypeNames = $manager->getRegisteredNames();
        natcasesort($dataTypeNames);
        foreach ($dataTypeNames as $dataTypeName) {
            $dataType = $manager->get($dataTypeName);
            if (!($dataType instanceof Fallback)) {
                $dataTypes[$dataTypeName] = $dataType;
            }
        }
        return $dataTypes;
    }

    /**
     * Get field templates for every data type.
     *
     * @return string
     */
    public function dataTypeTemplates() : string
    {
        $view = $this->getView();
        $templates = [];
        foreach ($this->dataTypes() as $dataTypeName => $dataType) {
            $fieldFieldset = new Fieldset('__INDEX__');
            $fieldFieldset->setLabel(sprintf(
                '<span class="field-name" data-new-field-name="%s"></span><span class="data-type-label">%s</span>',
                $view->escapeHtml($view->translate('New field')),
                $view->translate($dataType->getLabel())
            ));
            $fieldFieldset->setLabelOptions(['disable_html_escape' => true]);
            $fieldFieldset->setAttribute('class', $dataTypeName);

            $element = new Element\Hidden('o-module-datascribe:data_type');
            $element->setAttribute('value', $dataTypeName);
            $fieldFieldset->add($element);

            // Add the common "name" element.
            $element = new Element\Text('o-module-datascribe:name');
            $element->setLabel('Field name'); // @translate
            $element->setAttributes([
                'required' => true,
            ]);
            $fieldFieldset->add($element);

            // Add the common "description" element.
            $element = new Element\Text('o-module-datascribe:description');
            $element->setLabel('Field description'); // @translate
            $element->setAttributes([
                'required' => false,
            ]);
            $fieldFieldset->add($element);

            // Add the common "is_primary" element.
            $element = new Element\Checkbox('o-module-datascribe:is_primary');
            $element->setLabel('Field is primary'); // @translate
            $element->setAttributes([
                'required' => false,
            ]);
            $fieldFieldset->add($element);

            // Add the custom "data" elements.
            $fieldDataFieldset = new Fieldset('data');
            $fieldFieldset->add($fieldDataFieldset);
            $dataType->addFieldDataElements($fieldDataFieldset, []);

            $form = new Form;
            $form->add(new Fieldset('o-module-datascribe:field'));
            $form->get('o-module-datascribe:field')->add($fieldFieldset);
            $form->prepare();

            $templates[] = sprintf(
                '<span class="data-type-template" data-name="%s" data-template="%s"></span>',
                $view->escapeHtml($dataTypeName),
                $view->escapeHtml($view->formCollection($fieldFieldset))
            );
        }
        return implode("\n", $templates);
    }
}
