<?php
declare(strict_types=1);

namespace ILIAS\AssessmentQuestion\Gateway;

use ILIAS\AssessmentQuestion\DomainModel\QuestionDto;
use ILIAS\AssessmentQuestion\UserInterface\Web\AsqGUIElementFactory;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\QuestionComponent;
use ILIAS\AssessmentQuestion\UserInterface\Web\Form\QuestionFormGUI;
use ilAsqQuestionPageGUI;

/**
 * Class UIService
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 *
 * @package ILIAS\Services\AssessmentQuestion\PublicApi\Factory
 */
class UIService {
    public function getQuestionComponent(QuestionDto $question) : QuestionComponent {
        global $DIC;
        
        $DIC->language()->loadLanguageModule('asq');
        
        return new QuestionComponent($question);
    }
    
    public function getQuestionEditForm(QuestionDto $question) : QuestionFormGUI {
        return AsqGUIElementFactory::CreateQuestionForm($question);
    }
    
    public function getQuestionPage(QuestionDto $question_dto) : ilAsqQuestionPageGUI {
        $page_gui = new ilAsqQuestionPageGUI($question_dto->getContainerObjId(), $question_dto->getQuestionIntId(), $this->lng_key);
        $page_gui->setRenderPageContainer(false);
        $page_gui->setEditPreview(true);
        $page_gui->setEnabledTabs(false);
        $page_gui->setPresentationTitle($question_dto->getData()->getTitle());
        
        $question_component = $this->getQuestionComponent($question_dto);
        $page_gui->setQuestionComponent($question_component);
        
        return $page_gui;
    }
}