<?php

namespace luya\forms\tests;

use luya\forms\models\Form;
use luya\forms\models\Submission;
use luya\forms\models\SubmissionValue;
use luya\forms\SubmissionEmail;
use luya\testsuite\fixtures\NgRestModelFixture;

class SubmissionEmailTest extends BaseTestCase
{
    public function testMethods()
    {
        new NgRestModelFixture([
            'modelClass' => Form::class,
            'fixtureData' => [
                1 => [
                    'id' => 1,
                    'title' => '{"de":"Titel", "en":"Title"}',
                    'subject' => '{"de":"Subject", "en":"Subject"}',
                    'recipients' => '[{"value":"john@luya.io"},{"value":"foobar@luya.io"}]'
                ]
            ]
        ]);
        $this->createAdminLangFixture([
            1 => [
                'id' => 1,
                'name' => 'en',
                'short_code' => 'en',
                'is_deleted' => 0,
                'is_default' => 1,
            ]
        ]);

        new NgRestModelFixture([
            'modelClass' => SubmissionValue::class,
            'fixtureData' => [
                1 => [
                    'id' => 1,
                    'submission_id' => 1,
                    'attribute' => 'xyz',
                    'label' => 'XYZ',
                    'value' => 'value',
                ],
                2 => [
                    'id' => 2,
                    'submission_id' => 1,
                    'attribute' => 'xyz',
                    'label' => 'XYZ',
                    'value' => 'value',
                ],
            ]
        ]);

        new NgRestModelFixture([
            'modelClass' => Submission::class,
            'fixtureData' => [
                1 => [
                    'id' => 1,
                    'form_id' => 1,
                ]
            ]
        ]);
        $s = new SubmissionEmail(Submission::findOne(1));

        $this->assertSame('Subject', $s->getSubject());
        $this->assertSame(['john@luya.io', 'foobar@luya.io'], $s->getRecipients());
        $this->assertSame('<p>XYZ: value</p><p>XYZ: value</p>', $s->getSummaryHtml());
        $this->assertSame('', $s->getIntro());
        $this->assertSame('', $s->getOutro());
    }
}
