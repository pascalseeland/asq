<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class ClozeQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeQuestionGUI extends QuestionFormGUI {
    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {        
        return QuestionPlayConfiguration::create(
            ClozeEditor::readConfig(),
            ClozeScoring::readConfig());
    }

    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            ClozeEditorConfiguration::create('', []),
            ClozeScoringConfiguration::create());
    }

    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {
        foreach (ClozeEditor::generateFields($play->getEditorConfiguration()) as $field) {
            $this->addItem($field);
        }
        
        foreach (ClozeScoring::generateFields($play->getScoringConfiguration()) as $field) {
            $this->addItem($field);
        }
    }
    
    protected function postInit() {
        global $DIC;
        
        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'src/Questions/Cloze/ClozeAuthoring.js');
    }
}