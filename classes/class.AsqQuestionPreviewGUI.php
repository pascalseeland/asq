<?php
declare(strict_types=1);

use srag\CQRS\Aggregate\DomainObjectId;
use srag\asq\AsqGateway;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Hint\HintComponent;

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
    const CMD_SHOW_FEEDBACK = 'showFeedback';
    const CMD_SHOW_HINTS = 'showHints';
    const PARAM_REVISON_NAME = 'revisionName';
    
    /**
     * @var DomainObjectId
     */
    protected $question_id;

    /**
     * @var ?string
     */
    private $revision_name;
    
    /**
     * @var bool
     */
    private $show_feedback;
    
    /**
     * @var bool
     */
    private $show_hints;
    
    public function __construct(
        DomainObjectId $question_id
    ) {
        global $DIC;
        
        $this->question_id = $question_id;
        
        if (isset($_GET[self::PARAM_REVISON_NAME])) {
            $this->revision_name = $_GET[self::PARAM_REVISON_NAME];
            
            $DIC->ctrl()->setParameter(
                $this, self::PARAM_REVISON_NAME, $this->revision_name
            );
        }
    }
    
    public function executeCommand()
    {
        global $DIC;
        /* @var ILIAS\DI\Container $DIC */
        switch ($DIC->ctrl()->getNextClass()) {
            case strtolower(self::class):
            default:
                switch ($DIC->ctrl()->getCmd()) {
                    case self::CMD_SHOW_HINTS:
                        $this->show_hints = true;
                        
                        $this->showQuestion();
                        break;
                    case self::CMD_SHOW_FEEDBACK:
                        $this->show_feedback = true;
                        
                        $this->showQuestion();
                        break;
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

        if (is_null($this->revision_name)) {
            $question_dto = AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id->getId());
        } else {
            $question_dto = AsqGateway::get()->question()->getQuestionRevision($this->question_id->getId(), $this->revision_name);
        }
        
        $question_component = AsqGateway::get()->ui()->getQuestionComponent($question_dto);
        
        if ($this->show_feedback) {
            $question_component->setRenderFeedback(true);
        }
        
        $question_tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.question_preview_container.html', true, true, 'Services/AssessmentQuestion');
        $question_tpl->setVariable('FORMACTION', $DIC->ctrl()->getFormAction($this, self::CMD_SHOW_PREVIEW));
        $question_tpl->setVariable('QUESTION_OUTPUT', $question_component->renderHtml());
        
        if ($this->show_hints) {
            $hint_component = new HintComponent($question_dto->getQuestionHints());
            $question_tpl->setVariable('HINTS', $hint_component->getHtml());
        }

        if ($question_dto->hasFeedback()) {
            $question_tpl->setCurrentBlock('feedback_button');
            $question_tpl->setVariable('FEEDBACK_BUTTON_TITLE', $DIC->language()->txt('asq_feedback_button_title'));
            $question_tpl->parseCurrentBlock();
        }
        
        if ($question_dto->hasHints()) {
            $question_tpl->setCurrentBlock('hint_button');
            $question_tpl->setVariable('HINT_BUTTON_TITLE', $DIC->language()->txt('asq_hint_button_title'));
            $question_tpl->parseCurrentBlock();
        }
        
        
        $DIC->ui()->mainTemplate()->setContent($question_tpl->get());
    }
}
