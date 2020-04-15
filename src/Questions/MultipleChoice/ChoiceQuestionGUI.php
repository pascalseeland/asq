<?php
declare(strict_types=1);

namespace srag\asq\Questions\MultipleChoice;

use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class ChoiceQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class ChoiceQuestionGUI extends QuestionFormGUI {
    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            MultipleChoiceEditor::readConfig(),
            MultipleChoiceScoring::readConfig());
    }
    
    protected function postInit() {
        global $DIC;
        
        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'src/Questions/MultipleChoice/MultipleChoiceAuthoring.js');
    }
    
    /**
     * @param QuestionDto $question
     * @return QuestionDto
     */
    protected function processPostQuestion(QuestionDto $question) : QuestionDto
    {
        // strip image when multiline is selected
        if (!$question->getPlayConfiguration()->getEditorConfiguration()->isSingleLine()) {
            // remove from question
            $stripped_options = AnswerOptions::create(
                array_map(
                    function($option) {
                        return AnswerOption::create(
                            $option->getOptionId(),
                            ImageAndTextDisplayDefinition::create($option->getDisplayDefinition()->getText(), ''),
                            $option->getScoringDefinition());
                    }, 
                    $question->getAnswerOptions()->getOptions()
                )
            );
            
            $question->setAnswerOptions($stripped_options);
            $this->option_form->setAnswerOptions($stripped_options);
        }
        
        return $question;
    }
}
