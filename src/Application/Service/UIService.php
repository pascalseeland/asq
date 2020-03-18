<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use AsqQuestionPageGUI;
use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\AsqGUIElementFactory;
use srag\asq\UserInterface\Web\Component\QuestionComponent;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class UIService
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
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
    
    public function getQuestionPage(QuestionDto $question_dto) : AsqQuestionPageGUI {
        $page_gui = new AsqQuestionPageGUI($question_dto->getContainerObjId(), $question_dto->getQuestionIntId(), $this->lng_key);
        $page_gui->setRenderPageContainer(false);
        $page_gui->setEditPreview(true);
        $page_gui->setEnabledTabs(false);
        $page_gui->setPresentationTitle($question_dto->getData()->getTitle());
        
        $question_component = $this->getQuestionComponent($question_dto);
        $page_gui->setQuestionComponent($question_component);
        
        return $page_gui;
    }
}