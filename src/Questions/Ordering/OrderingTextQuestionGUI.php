<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering;

use ilNumberInputGUI;
use ilTextAreaInputGUI;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Scoring\EmptyScoringDefinition;
use srag\asq\UserInterface\Web\Component\Editor\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class OrderingQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingTextQuestionGUI extends QuestionFormGUI {
    const VAR_ORDERING_TEXT = 'otq_text';
    
    protected function canDisplayAnswerOptions() {
        return false;
    }
    
    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create();
    }
    
    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            OrderingEditorConfiguration::create(false, 
                                                !empty($_POST[OrderingEditor::VAR_MINIMUM_SIZE]) ? 
                                                    intval($_POST[OrderingEditor::VAR_MINIMUM_SIZE]) : 
                                                    null),
            OrderingScoring::readConfig());
    }
    
    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {
        global $DIC;
        
        $text = new ilTextAreaInputGUI($DIC->language()->txt('asq_ordering_text'), self::VAR_ORDERING_TEXT);
        $this->addItem($text);
        
        $minimum_size = new ilNumberInputGUI($DIC->language()->txt('asq_label_min_size'), OrderingEditor::VAR_MINIMUM_SIZE);
        $minimum_size->setInfo($DIC->language()->txt('asq_description_min_size'));
        $minimum_size->setSize(6);
        $this->addItem($minimum_size);
        
        $config = $play->getEditorConfiguration();
        if (!$config == null) {
            $minimum_size->setValue($config->getMinimumSize());
        }
        
        if (count($this->initial_question->getAnswerOptions()->getOptions())) {
            $question_text = [];
            
            foreach ($this->initial_question->getAnswerOptions()->getOptions() as $option) {
                $question_text[] = $option->getDisplayDefinition()->getText();
            }
            
            $text->setValue(implode(' ', $question_text));
        }
        
        foreach (OrderingScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
    }
    
    protected function readAnswerOptions(QuestionDto $question) : AnswerOptions {
        $text_input = $_POST[self::VAR_ORDERING_TEXT];

        $options = new AnswerOptions();
        
        $i = 1;
        if (!empty($text_input)) {
            $words = explode(' ', $text_input);
            
            foreach($words as $word) {
                $options->addOption(new AnswerOption(strval($i),
                                                     new ImageAndTextDisplayDefinition($word, ''),
                                                     new EmptyScoringDefinition()));
                $i += 1;
            }
        }
        
        return $options;
    }
}