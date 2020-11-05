<?php

namespace luya\forms;

use luya\forms\models\Submission;
use luya\helpers\StringHelper;

/**
 * E-Mail Submission Object
 * 
 * @author Basil Suter <git@nadar.io>
 * @since 1.0.0
 */
class SubmissionEmail
{
    /**
     * @var Submission
     */
    public $submission;

    /**
     * Constructor
     *
     * @param Submission $submission
     */
    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Returns the subject of the email.
     * 
     * If subject is not defined, the form title is returned
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->submission->form->subject ? $this->submission->form->subject : $this->submission->form->title;
    }

    /**
     * Returns all recipients
     *
     * @return array
     */
    public function getRecipients()
    {
        $recipients = $this->submission->form->recipients;

        $copyAttribute = $this->submission->form->copy_to_attribute;

        if ($copyAttribute) {
            foreach ($this->submission->values as $value) {
                if ($value->attribute == $copyAttribute) {
                    $recipients[] = $value->value;
                }
            }
        }

        return array_unique($recipients);
    }

    /**
     * Returns the summary of the form value
     *
     * @return string
     */
    public function getSummaryHtml()
    {
        $html = null;

        foreach ($this->submission->values as $value) {
            $html .= "<p>{$value->label}: {$value->value}</p>";
        }

        return $html;
    }

    /**
     * Returns the intro text for the email
     *
     * @return string
     */
    public function getIntro()
    {
        return StringHelper::template($this->submission->form->email_intro, $this->variablizeValues());
    }

    /**
     * Returns the outro text for the email
     *
     * @return string
     */
    public function getOutro()
    {
        return StringHelper::template($this->submission->form->email_outro, $this->variablizeValues());
    }

    /**
     * Get all variables with its value as array
     *
     * @return array
     */
    protected function variablizeValues()
    {
        $vars = [];
        foreach ($this->submission->values as $value) {
            $vars[$value->attribute] = $value->value;
        }

        return $vars;
    }
}
