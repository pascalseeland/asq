<?php
declare(strict_types=1);

namespace srag\asq\Questions\MultipleChoice;

use ilHiddenInputGUI;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDisplayDefinition;

/**
 * Class SingleChoiceQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SingleChoiceQuestionGUI extends ChoiceQuestionGUI {
	protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
	{
	    return QuestionPlayConfiguration::create
	    (
	        MultipleChoiceEditorConfiguration::create(false, 1),
	        MultipleChoiceScoringConfiguration::create());
	}
	
	protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
	{
	    $fields = MultipleChoiceEditor::generateFields($play->getEditorConfiguration());
	    
	    $hidden = new ilHiddenInputGUI(MultipleChoiceEditor::VAR_MCE_MAX_ANSWERS);
	    $hidden->setValue(1);
	    $fields[MultipleChoiceEditor::VAR_MCE_MAX_ANSWERS] = $hidden;
	    
	    foreach ($fields as $field) {
	        $this->addItem($field);
	    }
	}
	
	protected function getAnswerOptionDefinitions(?QuestionPlayConfiguration $play) : array { 
	    global $DIC;
	    
	    $definitions = array_merge(ImageAndTextDisplayDefinition::getFields($play),
	                               MultipleChoiceScoringDefinition::getFields($play));
	    
	    $definitions[MultipleChoiceScoringDefinition::VAR_MCSD_SELECTED]->setHeader($DIC->language()->txt('asq_label_points'));
	    
	    unset($definitions[MultipleChoiceScoringDefinition::VAR_MCSD_UNSELECTED]);
	    
	    return $definitions;
	}
}
