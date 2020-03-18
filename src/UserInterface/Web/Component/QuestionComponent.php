<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component;

use ilTemplate;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Component\Presenter\AbstractPresenter;
use srag\asq\UserInterface\Web\Component\Presenter\DefaultPresenter;

/**
 * Class QuestionComponent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionComponent
{
    const DEFAULT_SUBMIT_CMD = "submitAnswer";
    const DEFAULT_SHOW_FEEDBACK_CMD = "showFeedback";
    const DEFAULT_GET_HINT_CMD = "getHint";
    
    /**
     * @var QuestionDto
     */
    private $question_dto;
    /**
     * @var AbstractPresenter
     */
    private $presenter;
    /**
     * @var AbstractEditor
     */
    private $editor;

    public function __construct(QuestionDto $question_dto)
    {
        $this->question_dto = $question_dto;

        $presenter_class = DefaultPresenter::class;
        $presenter = new $presenter_class($question_dto);

        $editor_class = $question_dto->getPlayConfiguration()->getEditorConfiguration()->configurationFor();
        $editor = new $editor_class($question_dto);

        $this->presenter = $presenter;
        $this->editor = $editor;
    }


    public function renderHtml() : string
    {
        global $DIC;

        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.question_view.html', true, true);

        $tpl->setCurrentBlock('question');
        $tpl->setVariable('SCORE_COMMAND', self::DEFAULT_SUBMIT_CMD);
        $tpl->setVariable('QUESTION_OUTPUT', $this->presenter->generateHtml($this->editor));
        $tpl->setVariable('BUTTON_TITLE', $DIC->language()->txt('check'));
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }


    public function readAnswer()
    {
        return $this->editor->readAnswer();
    }


    public function setAnswer(Answer $answer)
    {
        $this->editor->setAnswer($answer->getValue());
    }
}