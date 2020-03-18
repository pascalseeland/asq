<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;


use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;
use srag\asq\UserInterface\Web\Form\Config\AnswerOptionForm;

/**
 * Class ErrorTextQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextQuestionGUI extends QuestionFormGUI {
    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create
        (
            ErrorTextEditorConfiguration::create('', ErrorTextEditor::DEFAULT_TEXTSIZE_PERCENT),
            ErrorTextScoringConfiguration::create()
            );
    }
    
    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            ErrorTextEditor::readConfig(),
            ErrorTextScoring::readConfig());
    }

    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {
        foreach (ErrorTextEditor::generateFields($play->getEditorConfiguration()) as $field) {
            $this->addItem($field);
        }
        
        foreach (ErrorTextScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
    }
    
    protected function getAnswerOptionConfiguration() {
        return [
            AnswerOptionForm::OPTION_HIDE_ADD_REMOVE => true,
            AnswerOptionForm::OPTION_HIDE_EMPTY => true
        ];
    }
    
    protected function postInit() {
        global $DIC;
        
        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'src/Questions/ErrorText/ErrorTextAuthoring.js');
    }
}