<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;
/**
 * Class FormulaQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaQuestionGUI extends QuestionFormGUI {
    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            FormulaEditorConfiguration::create(),
            FormulaScoringConfiguration::create());
    }
    
    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            FormulaEditor::readConfig(),
            FormulaScoring::readConfig());
    }
    
    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {        
        foreach (FormulaEditor::generateFields($play->getEditorConfiguration()) as $field) {
            $this->addItem($field);
        }
        
        foreach (FormulaScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
    }
    
    protected function postInit() {
        global $DIC;
        $question_text = $this->getItemByPostVar(QuestionFormGUI::VAR_QUESTION);
        $question_text->setInfo($DIC->language()->txt('asq_info_question') . $this->getParsebutton());
        $question_text->setUseRte(false);
        
        $this->option_form->setInfo($DIC->language()->txt('asq_info_results'));
        
        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'src/Questions/Formula/FormulaAuthoring.js');
    }
    
    private function getParseButton() : string {
        global $DIC;
        return '<br /><input type="button" value="' . $DIC->language()->txt('asq_parse_question') . '" class="js_parse_question btn btn-default" />';
    }
    
    protected function getAnswerOptionConfiguration() {
        return [AsqTableInput::OPTION_HIDE_ADD_REMOVE => true];
    }
}