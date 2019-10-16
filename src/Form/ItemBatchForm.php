<?php
namespace Datascribe\Form;

use Zend\Form\Form;

class ItemBatchForm extends Form
{
    public function init()
    {
        $inputFilter = $this->getInputFilter();
    }
}
