<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim;

use ilCheckboxInputGUI;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;
use srag\asq\UserInterface\Web\Form\Config\AnswerOptionForm;

/**
 * Class KprimChoiceQuestionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceQuestionGUI extends QuestionFormGUI {
    
    const HALFPOINTS_AT = 3;
    
    /**
     * QuestionFormGUI constructor.
     *
     * @param QuestionDto $question
     */
    public function __construct($question) {
        while (count($question->getAnswerOptions()->getOptions()) < 4) {
            $question->getAnswerOptions()->addOption(null);
        }
        
        parent::__construct($question);
        
        $this->option_form->setInfo($this->lang->txt('asq_kprim_information'));
    }

    protected function createDefaultPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            KprimChoiceEditorConfiguration::create(),
            KprimChoiceScoringConfiguration::create());
    }
    
    protected function readPlayConfiguration(): QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(
            KprimChoiceEditor::readConfig(),
            KprimChoiceScoring::readConfig());
    }

    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void
    {
        $fields = array_merge(KprimChoiceEditor::generateFields($play->getEditorConfiguration()),
                              KprimChoiceScoring::generateFields($play->getScoringConfiguration()));
        
        /** @var $old \ilTextInputGUI */
        $old = $fields[KprimChoiceScoring::VAR_HALF_POINTS];
        $new = new ilCheckboxInputGUI($old->getTitle(), KprimChoiceScoring::VAR_HALF_POINTS);
        $new->setValue(self::HALFPOINTS_AT);
        $new->setInfo($old->getInfo());
        if (!is_null($play->getScoringConfiguration())) {
            $new->setChecked($play->getScoringConfiguration()->getHalfPointsAt() === self::HALFPOINTS_AT);
        }
        $fields[KprimChoiceScoring::VAR_HALF_POINTS] = $new;
        
        foreach ($fields as $field) {
            $this->addItem($field);
        }
    }

    protected function getAnswerOptionConfiguration() {
        return [
            AnswerOptionForm::OPTION_ORDER => true,
            AnswerOptionForm::OPTION_HIDE_ADD_REMOVE => true
        ];
    }}
