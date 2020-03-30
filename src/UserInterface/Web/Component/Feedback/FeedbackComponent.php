<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Feedback;

use ilTemplate;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Scoring\ScoringComponent;
use srag\asq\Domain\QuestionDto;

/**
 * Class FeedbackComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class FeedbackComponent
{
    const FEEDBACK_FOCUS_ANCHOR = 'focus';

    /**
     * @var ScoringComponent
     */
    private $scoring_component;
    /**
     * @var AnswerFeedbackComponent
     */
    private $answer_feedback_component;

    public function __construct(QuestionDto $question_dto, Answer $answer)
    {
        $this->scoring_component = new ScoringComponent($question_dto, $answer);
        $this->answer_feedback_component = new AnswerFeedbackComponent($question_dto, $answer);
    }


    public function getHtml() : string
    {
        global $DIC;

        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.feedback.html', true, true);

        $tpl->setCurrentBlock('feedback_header');
        $tpl->setVariable('FEEDBACK_HEADER', $DIC->language()->txt('asq_answer_feedback_header'));
        $tpl->parseCurrentBlock();

        $tpl->setCurrentBlock('answer_feedback');
        $tpl->setVariable('ANSWER_FEEDBACK', $this->answer_feedback_component->getHtml());
        $tpl->parseCurrentBlock();

        $tpl->setCurrentBlock('answer_scoring');
        $tpl->setVariable('ANSWER_SCORING', $this->scoring_component->getHtml());
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }


    public function readAnswer() : string
    {
        return $this->editor->readAnswer();
    }


    public function setAnswer(Answer $answer)
    {
        $this->editor->setAnswer($answer->getValue());
    }
}