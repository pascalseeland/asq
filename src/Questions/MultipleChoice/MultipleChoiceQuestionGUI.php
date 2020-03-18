<?php
declare(strict_types=1);

namespace srag\asq\Questions\MultipleChoice;

use srag\asq\Domain\Model\QuestionPlayConfiguration;

/**
 * Class MultipleChoiceQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceQuestionGUI extends ChoiceQuestionGUI {
    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            MultipleChoiceEditorConfiguration::create(),
            MultipleChoiceScoringConfiguration::create());
    }
    
    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {
        foreach (MultipleChoiceEditor::generateFields($play->getEditorConfiguration()) as $field) {
            $this->addItem($field);
        }
        
        foreach (MultipleChoiceScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
    }
}
