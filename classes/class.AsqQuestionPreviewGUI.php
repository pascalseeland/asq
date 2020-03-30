<?php
declare(strict_types=1);

use srag\CQRS\Aggregate\DomainObjectId;
use srag\asq\AsqGateway;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Feedback\AnswerFeedbackComponent;
use srag\asq\UserInterface\Web\Component\Feedback\FeedbackComponent;
use srag\asq\UserInterface\Web\Component\Scoring\ScoringComponent;

/**
 * Class AsqQuestionPreviewGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AsqQuestionPreviewGUI
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
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question_component->setRenderFeedback(true);
        }
        
        $question_tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.question_preview_container.html', true, true, 'Services/AssessmentQuestion');
        $question_tpl->setVariable('FORMACTION', $DIC->ctrl()->getFormAction($this, self::CMD_SHOW_PREVIEW));
        $question_tpl->setVariable('QUESTION_OUTPUT', $question_component->renderHtml());
        $question_tpl->setVariable('FEEDBACK_BUTTON_TITLE', $DIC->language()->txt('asq_feedback_button_title'));

        $DIC->ui()->mainTemplate()->setContent($question_tpl->get());
    }
}
