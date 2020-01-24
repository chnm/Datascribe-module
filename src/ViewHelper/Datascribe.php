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
        foreach ($manager->getRegisteredNames() as $dataTypeName) {
            $dataType = $manager->get($dataTypeName);
            if (!($dataType instanceof Fallback)) {
                $dataTypes[$dataTypeName] = $dataType;
            }
        }
        return $dataTypes;
    }

    /**
     * Get the fields of a dataset.
     *
     * @param DatascribeDatasetRepresentation $dataset
     * @return string
     */
    public function fields(DatascribeDatasetRepresentation $dataset) : string
    {
        $manager = $this->services->get('Datascribe\DataTypeManager');
        $view = $this->getView();
        $fields = [];
        foreach ($dataset->fields() as $field) {
            $dataType = $manager->get($field->getDataType());

            $fieldset = new Fieldset($field->getPosition());
            $fieldset->setLabel(sprintf(
                '<span class="field-label">%s</span><span class="data-type-label">%s</span>',
                $field->getLabel(),
                $view->translate($dataType->getLabel())
            ));
            $fieldset->setLabelOptions(['disable_html_escape' => true]);
            $fieldset->setAttribute('class', $field->getDataType());

            $element = new Element\Hidden('o:id');
            $element->setAttribute('value', $field->getId());
            $fieldset->add($element);

            $this->addFieldElements($fieldset, $field);
            $dataType->addFieldElements($fieldset, $field->getData());

            $form = new Form;
            $form->add(new Fieldset('o-module-datascribe:field'));
            $form->get('o-module-datascribe:field')->add($fieldset);
            $form->prepare();

            $fields[] = $view->formCollection($fieldset);
        }
        return implode("\n", $fields);
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
            $fieldset = new Fieldset('__INDEX__');
            $fieldset->setLabel(sprintf(
                '<span class="field-label" data-new-field-label="%s"></span><span class="data-type-label">%s</span>',
                $view->escapeHtml($view->translate('New field')),
                $view->translate($dataType->getLabel())
            ));
            $fieldset->setLabelOptions(['disable_html_escape' => true]);
            $fieldset->setAttribute('class', $dataTypeName);

            $element = new Element\Hidden('o-module-datascribe:data_type');
            $element->setAttribute('value', $dataTypeName);
            $fieldset->add($element);

            $this->addFieldElements($fieldset, null);
            $dataType->addFieldElements($fieldset, []);

            $form = new Form;
            $form->add(new Fieldset('o-module-datascribe:field'));
            $form->get('o-module-datascribe:field')->add($fieldset);
            $form->prepare();

            $templates[] = sprintf(
                '<span class="data-type-template" data-name="%s" data-template="%s"></span>',
                $view->escapeHtml($dataTypeName),
                $view->escapeHtml($view->formCollection($fieldset))
            );
        }
        return implode("\n", $templates);
    }

    /**
     * Add field elements common to all fields.
     *
     * @param Fieldset $fieldset
     * @param ?DatascribeField $field
     */
    protected function addFieldElements(Fieldset $fieldset, ?DatascribeField $field) : void
    {
        $element = new Element\Text('o-module-datascribe:label');
        $element->setLabel('Field label'); // @translate
        $element->setAttributes([
            'required' => true,
            'value' => $field ? $field->getLabel() : null,
        ]);
        $fieldset->add($element);

        $element = new Element\Text('o-module-datascribe:info');
        $element->setLabel('Field info'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $field ? $field->getInfo() : null,
        ]);
        $fieldset->add($element);

        $element = new Element\Checkbox('o-module-datascribe:is_primary');
        $element->setLabel('Field is primary'); // @translate
        $element->setAttributes([
            'required' => false,
            'value' => $field ? $field->getIsPrimary() : null,
        ]);
        $fieldset->add($element);
    }
}
