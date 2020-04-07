<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint\Form;

use ILIAS\UI\Component\Layout\Page\Page;
use ilRTE;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Hint\QuestionHint;
use srag\asq\Domain\Model\Hint\QuestionHints;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

/**
 * Class HintFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintFormGUI extends \ilPropertyFormGUI
{
    const HINT_POSTVAR = 'hints';
    const HINT_CONTENT_POSTVAR = 'hint_content';
    const HINT_POINTS_POSTVAR = 'hint_points';
    
    /**
     * @var QuestionDto
     */
    protected $question_dto;

    /**
     * QuestionHintFormGUI constructor.
     *
     * @param Page        $page
     * @param QuestionDto $questionDto
     */
    public function __construct(QuestionDto $question_dto) {
        global $DIC;
        /* @var \ILIAS\DI\Container $DIC */

        parent::__construct();

        $this->question_dto = $question_dto;

        $this->setTitle($DIC->language()->txt('asq_feedback_form_title'));

        $this->initForm();
        
        $rtestring = ilRTE::_getRTEClassname();
        include_once "./Services/RTE/classes/class.$rtestring.php";
        $rte = new $rtestring();
        $rte->addRTESupport(55, 'blah');
        
        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'js/AssessmentQuestionAuthoring.js');
    }


    protected function initForm()
    {
        global $DIC;
        /* @var \ILIAS\DI\Container $DIC */

        $this->setTitle(sprintf($DIC->language()->txt('asq_question_hints_form_header'), $this->question_dto->getData()->getTitle()));

        $hint_table = new AsqTableInput(
            $DIC->language()->txt('asq_hints'), 
            self::HINT_POSTVAR, 
            $this->getHintData(), 
            $this->getTableDefinitions(),
            [AsqTableInput::OPTION_ORDER]); 
        
        $this->addItem($hint_table);
    }

    private function getHintData() : array {
        if (!$this->question_dto->hasHints()) {
            return [];
        }
        
        return array_map(function($hint) {
            return [
                self::HINT_CONTENT_POSTVAR => $hint->getContent(), 
                self::HINT_POINTS_POSTVAR => $hint->getPointDeduction()];
        }, $this->question_dto->getQuestionHints()->getHints());
    }
    
    private function getTableDefinitions() : array {
        global $DIC;
        
        return [
            new AsqTableInputFieldDefinition(
                $DIC->language()->txt('asq_question_hints_label_hint'), 
                AsqTableInputFieldDefinition::TYPE_TEXT_AREA, 
                self::HINT_CONTENT_POSTVAR),
            new AsqTableInputFieldDefinition(
                $DIC->language()->txt('asq_question_hints_label_points_deduction'), 
                AsqTableInputFieldDefinition::TYPE_NUMBER, 
                self::HINT_POINTS_POSTVAR)
        ];
    }
    
    public function getHintsFromPost() : QuestionHints {
        $index = 0;
        return QuestionHints::create(
            array_map(
                function($raw_hint) use ($index) {
                    $index += 1;
                    
                    return QuestionHint::create(
                        strval($index),
                        AsqHtmlPurifier::getInstance()->purify($raw_hint[self::HINT_CONTENT_POSTVAR]), 
                        floatval($raw_hint[self::HINT_POINTS_POSTVAR]));
                }, 
                AsqTableInput::readValuesFromPost(self::HINT_POSTVAR, $this->getTableDefinitions())
            )
        );
    }
}