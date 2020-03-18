<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class MatchingQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingQuestionGUI extends QuestionFormGUI {
    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            MatchingEditorConfiguration::create(),
            MatchingScoringConfiguration::create());
    }
    
    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            MatchingEditor::readConfig(),
            MatchingScoring::readConfig());
    }
    
    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {
        foreach (MatchingScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
        
        foreach (MatchingEditor::generateFields($play->getEditorConfiguration()) as $field) {
            $this->addItem($field);
        }
    }
    
    protected function postInit() {
        global $DIC;
        
        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'src/Questions/Matching/MatchingAuthoring.js');
    }
}
