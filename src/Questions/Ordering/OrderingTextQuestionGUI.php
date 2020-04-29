<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Ordering;

use ilNumberInputGUI;
use ilTextAreaInputGUI;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\Form\QuestionFormGUI;

/**
 * Class OrderingQuestionGUI
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingTextQuestionGUI extends QuestionFormGUI
{

    const VAR_ORDERING_TEXT = 'otq_text';

    /**
     * {@inheritdoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormGUI::canDisplayAnswerOptions()
     */
    protected function canDisplayAnswerOptions() : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormGUI::createDefaultPlayConfiguration()
     */
    protected function createDefaultPlayConfiguration() : QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create();
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormGUI::readPlayConfiguration()
     */
    protected function readPlayConfiguration() : QuestionPlayConfiguration
    {
        return QuestionPlayConfiguration::create(OrderingEditorConfiguration::create(false, ! empty($_POST[OrderingEditor::VAR_MINIMUM_SIZE]) ? intval($_POST[OrderingEditor::VAR_MINIMUM_SIZE]) : null), OrderingScoring::readConfig());
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormGUI::initiatePlayConfiguration()
     */
    protected function initiatePlayConfiguration(?QuestionPlayConfiguration $play) : void
    {
        global $DIC;

        $text = new ilTextAreaInputGUI($DIC->language()->txt('asq_ordering_text'), self::VAR_ORDERING_TEXT);
        $this->addItem($text);

        if (! is_null($this->initial_question->getAnswerOptions()) &&
            count($this->initial_question->getAnswerOptions()->getOptions())) {
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

    /**
     * {@inheritdoc}
     * @see \srag\asq\UserInterface\Web\Form\QuestionFormGUI::readAnswerOptions()
     */
    protected function readAnswerOptions(QuestionDto $question) : AnswerOptions
    {
        $text_input = $_POST[self::VAR_ORDERING_TEXT];

        $options = [];

        $i = 1;
        if (! empty($text_input)) {
            $words = explode(' ', $text_input);

            foreach ($words as $word) {
                $options[] = AnswerOption::create(
                    strval($i),
                    ImageAndTextDisplayDefinition::create($word, ''),
                    EmptyDefinition::create()
                );

                $i += 1;
            }
        }

        return Answeroptions::create($options);
    }
}