<?php
namespace Datascribe\Form\ViewHelper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormCheckbox;

class DatascribeFormCheckbox extends FormCheckbox
{
    public function render(ElementInterface $element)
    {
        $view = $this->getView();
        $view->headScript()->appendFile($view->assetUrl('js/admin/data-type/checkbox.js', 'Datascribe'));

        return sprintf(
            '<div class="datascribe-checkbox">%s</div>',
            parent::render($element)
        );
    }
}
