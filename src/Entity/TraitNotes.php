<?php
namespace Datascribe\Entity;

trait TraitNotes
{
    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $transcriberNotes;

    /**
     * @Column(
     *     type="text",
     *     nullable=true
     * )
     */
    protected $reviewerNotes;

    public function setTranscriberNotes(?string $transcriberNotes) : void
    {
        $this->transcriberNotes = $transcriberNotes;
    }

    public function getTranscriberNotes() : ?string
    {
        return $this->transcriberNotes;
    }

    public function setReviewerNotes(?string $reviewerNotes) : void
    {
        $this->reviewerNotes = $reviewerNotes;
    }

    public function getReviewerNotes() : ?string
    {
        return $this->reviewerNotes;
    }
}
