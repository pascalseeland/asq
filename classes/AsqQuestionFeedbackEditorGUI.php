<?php
declare(strict_types=1);

use srag\asq\AsqGateway;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\Feedback\Form\QuestionFeedbackFormGUI;

/**
 * Class AsqQuestionFeedbackEditorGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_Calls AsqQuestionFeedbackEditorGUI: ilAsqGenericFeedbackPageGUI
 * @ilCtrl_Calls AsqQuestionFeedbackEditorGUI: ilAsqAnswerOptionFeedbackPageGUI
 */
class AsqQuestionFeedbackEditorGUI
{

    const CMD_SHOW_FEEDBACK_FORM = 'showFeedbackForm';
    const CMD_SAVE_FEEDBACK = 'saveFeedback';
    
    /**
     * @var QuestionDto
     */
    protected $question_dto;

    /**
     * @param QuestionDto $question_dto
     */
    public function __construct(
        QuestionDto $question_dto
    ) {
        $this->question_dto = $question_dto;
    }


    /**
     * @throws ilCtrlException
     */
    public function executeCommand()
    {
        global $DIC;

        $cmd = $DIC->ctrl()->getCmd(self::CMD_SHOW_FEEDBACK_FORM);
        $this->{$cmd}();
    }

    protected function saveFeedback() {
        global $DIC;
        
        $form = $this->createForm();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
            $form->checkInput()) 
        {
            $new_feedback = $form->getFeedbackFromPost();
            $this->question_dto->setFeedback($new_feedback);
            AsqGateway::get()->question()->saveQuestion($this->question_dto);
            ilutil::sendSuccess("Question Saved", true);
        }
            
        $DIC->ui()->mainTemplate()->setContent($form->getHTML());
    }

    protected function showFeedbackForm()
    {
        global $DIC;

        $form = $this->createForm();

        $DIC->ui()->mainTemplate()->setContent($form->getHTML());
    }

    private function createForm()
    {
        global $DIC;
        $form = new QuestionFeedbackFormGUI($this->question_dto);
        $form->setFormAction($DIC->ctrl()->getFormAction($this, self::CMD_SHOW_FEEDBACK_FORM));
        $form->addCommandButton(self::CMD_SAVE_FEEDBACK, $DIC->language()->txt('save'));
        $form->addCommandButton(self::CMD_SHOW_FEEDBACK_FORM, $DIC->language()->txt('cancel'));
        return $form;
    }

}
