<?php
namespace Datascribe\Form\ViewHelper;

use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormText;

class DatascribeFormText extends FormText
{
    public function render(ElementInterface $element)
    {
        $fieldData = $element->getOption('datascribe_field_data');
        if (!isset($fieldData['datalist'])) {
            return parent::render($element);
        }

        $view = $this->getView();

        $datalistId = substr(md5(rand()), 0, 7);
        $element->setAttribute('list', $datalistId);

        $datalistOptions = [];
        foreach ($fieldData['datalist'] as $option) {
            $datalistOptions[] = sprintf('<option value="%s">', $view->escapeHtml($option));
        }

        return sprintf(
            '%s<datalist id="%s">%s</datalist>',
            parent::render($element),
            $view->escapeHtml($datalistId),
            implode('', $datalistOptions)
        );
    }
}
