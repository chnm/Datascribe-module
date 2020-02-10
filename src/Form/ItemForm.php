<?php
namespace Datascribe\Form;

use Doctrine\ORM\EntityManager;
use Omeka\Entity\User;
use Zend\Form\Element;
use Zend\Form\Form;

class ItemForm extends Form
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var User
     */
    protected $user;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function init()
    {
        $item = $this->getOption('item');

        // transcriber_notes textarea
        $element = new Element\Textarea('o-module-datascribe:transcriber_notes');
        $element->setValue($item->transcriberNotes());
        if (!$item->userIsAllowed('datascribe_edit_transcriber_notes')) {
            $element->setAttribute('disabled', true);
        }
        $this->add($element);

        // reviewer_notes textarea
        $element = new Element\Textarea('o-module-datascribe:reviewer_notes');
        $element->setValue($item->reviewerNotes());
        if (!$item->userIsAllowed('datascribe_edit_reviewer_notes')) {
            $element->setAttribute('disabled', true);
        }
        $this->add($element);

        // submit_action select
        $element = new Element\Select('submit_action');
        $valueOptions = [
            '[No change]', // @translate
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
            '[No change]', // @translate
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

        // Once an item is marked as approved, transcribers can no longer perform
        // lock/unlock or submit/unsubmit actions, nor can they add new records
        // to the item or edit existing records. Work can continue on the item only
        // by admin/reviewer.

        // Render only what's relevent to current user's role and depending on the
        // current state of the item.

        // lock_action
        // - Unlock (unlock): if admin/reviewer -OR- locked to current user
        // - Lock to me (lock): if admin/reviewer -OR- unlocked
        // - Lock to... (ID#): if admin/reviewer

        // priority_action
        // - Mark as prioritized (prioritized): if admin/reviewer -AND- not already marked as prioritized
        // - Mark as not prioritized (not_prioritized): if admin/reviewer -AND- not already not marked as prioritized

    }
}
