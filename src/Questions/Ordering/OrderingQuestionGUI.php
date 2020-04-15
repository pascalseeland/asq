<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;
use srag\asq\UserInterface\Web\Form\Config\AnswerOptionForm;

/**
 * Class OrderingQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingQuestionGUI extends QuestionFormGUI {
    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            OrderingEditorConfiguration::create(),
            OrderingScoringConfiguration::create());
    }
    
    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            OrderingEditor::readConfig(),
            OrderingScoring::readConfig());
    }
    
    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {
        foreach (OrderingEditor::generateFields($play->getEditorConfiguration()) as $field) {
            $this->addItem($field);
        }
        
        foreach (OrderingScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
    }
    
    protected function getAnswerOptionConfiguration() {
        return [ AnswerOptionForm::OPTION_ORDER => true ];
    }
}