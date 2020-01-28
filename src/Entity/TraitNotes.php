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
        if (is_string($transcriberNotes) && '' === trim($transcriberNotes)) {
            $transcriberNotes = null;
        }
        $this->transcriberNotes = $transcriberNotes;
    }

    public function getTranscriberNotes() : ?string
    {
        return $this->transcriberNotes;
    }

    public function setReviewerNotes(?string $reviewerNotes) : void
    {
        if (is_string($reviewerNotes) && '' === trim($reviewerNotes)) {
            $reviewerNotes = null;
        }
        $this->transcriberNotes = $reviewerNotes;
    }

    public function getReviewerNotes() : ?string
    {
        return $this->reviewerNotes;
    }
}
