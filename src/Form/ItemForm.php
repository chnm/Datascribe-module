<?php
namespace Datascribe\Form;

use Zend\Form\Element;

class ItemForm extends AbstractForm
{
    public function init()
    {
        $item = $this->getOption('item');

        // transcriber_notes textarea
        if ($item->userIsAllowed('datascribe_edit_transcriber_notes')) {
            $element = new Element\Textarea('o-module-datascribe:transcriber_notes');
            $element->setValue($item->transcriberNotes());
            $this->add($element);
        }

        // reviewer_notes textarea
        if ($item->userIsAllowed('datascribe_edit_reviewer_notes')) {
            $element = new Element\Textarea('o-module-datascribe:reviewer_notes');
            $element->setValue($item->reviewerNotes());
            $this->add($element);
        }

        // submit_action select
        $element = new Element\Select('submit_action');
        $valueOptions = [
            '' => '[No change]', // @translate
        ];
        if ($item->userIsAllowed('datascribe_mark_item_submitted')) {
            $valueOptions['submitted'] = 'Submit for review'; // @translate
        }
        if ($item->userIsAllowed('datascribe_mark_item_not_submitted')) {
            $valueOptions['not_submitted'] = 'Mark as not submitted'; // @translate
        }
        $element->setValueOptions($valueOptions);
        $element->setAttribute('class', 'chosen-select');
        $this->add($element);

        // review_action select
        $element = new Element\Select('review_action');
        $valueOptions = [
            '' => '[No change]', // @translate
        ];
        if ($item->userIsAllowed('datascribe_mark_item_approved')) {
            $valueOptions['approved'] = 'Mark as approved'; // @translate
        }
        if ($item->userIsAllowed('datascribe_mark_item_not_approved')) {
            $valueOptions['not_approved'] = 'Mark as not approved'; // @translate
        }
        if ($item->userIsAllowed('datascribe_mark_item_not_reviewed')) {
            $valueOptions['not_reviewed'] = 'Mark as not reviewed'; // @translate
        }
        $element->setValueOptions($valueOptions);
        $element->setAttribute('class', 'chosen-select');
        $this->add($element);

        // lock_action select
        $element = new Element\Select('lock_action');
        $valueOptions = [
            '' => '[No change]', // @translate
        ];
        if ($item->userIsAllowed('datascribe_unlock_item')) {
            $valueOptions['unlock'] = 'Unlock'; // @translate
        }
        if ($item->userIsAllowed('datascribe_lock_item_to_self')) {
            $valueOptions['lock'] = 'Lock to me'; // @translate
        }
        if ($item->userIsAllowed('datascribe_lock_item_to_other')) {
            $valueOptions = $this->getLockToOtherValueOptions($valueOptions);
        }
        $element->setValueOptions($valueOptions);
        $element->setAttribute('class', 'chosen-select');
        $this->add($element);

        // priority_action select
        $element = new Element\Select('priority_action');
        $valueOptions = [
            '' => '[No change]', // @translate
        ];
        if ($item->userIsAllowed('datascribe_mark_item_prioritized')) {
            $valueOptions['prioritized'] = 'Mark as prioritized'; // @translate
        }
        if ($item->userIsAllowed('datascribe_mark_item_not_prioritized')) {
            $valueOptions['not_prioritized'] = 'Mark as not prioritized'; // @translate
        }
        $element->setValueOptions($valueOptions);
        $element->setAttribute('class', 'chosen-select');
        $this->add($element);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'submit_action',
            'allow_empty' => true,
        ]);
        $inputFilter->add([
            'name' => 'review_action',
            'allow_empty' => true,
        ]);
        $inputFilter->add([
            'name' => 'lock_action',
            'allow_empty' => true,
        ]);
        $inputFilter->add([
            'name' => 'priority_action',
            'allow_empty' => true,
        ]);
    }
}
