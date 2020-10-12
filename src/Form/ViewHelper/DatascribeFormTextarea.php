<?php
namespace Datascribe\Form\ViewHelper;

use Laminas\Form\View\Helper\FormTextarea;

/**
 * Polyfill for ZF3's FormTextarea, which does not allow "minlength" attribute.
 */
class DatascribeFormTextarea extends FormTextarea
{
    public function __construct()
    {
        $this->addValidAttribute('minlength');
    }
}
