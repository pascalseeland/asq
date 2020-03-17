<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

use ILIAS\AssessmentQuestion\Gateway\AsqGateway;
use ILIAS\AssessmentQuestion\UserInterface\Web\PathHelper;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\Feedback\AnswerFeedbackComponent;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\Feedback\FeedbackComponent;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\Scoring\ScoringComponent;
use srag\CQRS\Aggregate\DomainObjectId;

/**
 * Class ilAsqQuestionPreviewGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ilAsqQuestionPreviewGUI
{

    const CMD_SHOW_PREVIEW = 'showPreview';
    const CMD_SHWOW_Feedback = 'showFeedback';

    /**
     * @var DomainObjectId
     */
    protected $question_id;

    public function __construct(
        DomainObjectId $question_id
    ) {
        $this->question_id = $question_id;
    }


    public function executeCommand()
    {
        global $DIC;
        /* @var ILIAS\DI\Container $DIC */
        switch ($DIC->ctrl()->getNextClass()) {
            case strtolower(self::class):
            default:
                switch ($DIC->ctrl()->getCmd()) {
                    case self::CMD_SHWOW_Feedback:
                    case self::CMD_SHOW_PREVIEW:
                    default:
                        $this->showQuestion();
                        break;
                }
        }
    }

    public function showQuestion()
    {
        global $DIC;

        $question_dto = AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id->getId());
        
        $question_component = AsqGateway::get()->ui()->getQuestionComponent($question_dto);
        
        $question_tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.question_preview_container.html', true, true, 'Services/AssessmentQuestion');
        $question_tpl->setVariable('FORMACTION', $DIC->ctrl()->getFormAction($this, self::CMD_SHOW_PREVIEW));
        $question_tpl->setVariable('QUESTION_OUTPUT', $question_component->renderHtml());
        $question_tpl->setVariable('FEEDBACK_BUTTON_TITLE', $DIC->language()->txt('asq_feedback_button_title'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $feedback_component = new FeedbackComponent(
                new ScoringComponent($question_dto, $question_component->readAnswer()), 
                new AnswerFeedbackComponent($question_dto, $question_component->readAnswer()));
            $question_tpl->setCurrentBlock('instant_feedback');
            $question_tpl->setVariable('INSTANT_FEEDBACK',$feedback_component->getHtml());
            $question_tpl->parseCurrentBlock();
        }

        $DIC->ui()->mainTemplate()->setContent($question_tpl->get());
    }
}
