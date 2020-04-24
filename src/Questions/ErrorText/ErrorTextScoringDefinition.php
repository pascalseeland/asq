<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\InputHelper;

/**
 * Class ErrorTextScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextScoringDefinition extends AnswerDefinition {
    const VAR_WRONG_TEXT = 'etsd_wrong_text';
    const VAR_WORD_INDEX = 'etsd_word_index';
    const VAR_WORD_LENGTH = 'etsd_word_length';
    const VAR_CORRECT_TEXT = 'etsd_correct_text' ;
    const VAR_POINTS = 'etsd_points';

    /**
     * @var ?int
     */
    protected $wrong_word_index;
    /**
     * @var ?int
     */
    protected $wrong_word_length;
    /**
     * @var ?string
     */
    protected $correct_text;
    /**
     * @var ?float
     */
    protected $points;

    /**
     * @var array
     */
    private static $error_text_words;

    /**
     * @param int $wrong_word_index
     * @param int $wrong_word_length
     * @param string $correct_text
     * @param float $points
     * @return \srag\asq\Questions\ErrorText\ErrorTextScoringDefinition
     */
    public static function create(?int $wrong_word_index, ?int $wrong_word_length, ?string $correct_text, ?float $points)
    {
        $object = new ErrorTextScoringDefinition();
        $object->wrong_word_index = $wrong_word_index;
        $object->wrong_word_length = $wrong_word_length;
        $object->correct_text = $correct_text;
        $object->points = $points;
        return $object;
    }

    /**
     * @return int
     */
    public function getWrongWordIndex() : ?int
    {
        return $this->wrong_word_index;
    }

    /**
     * @return int
     */
    public function getWrongWordLength() : ?int
    {
        return $this->wrong_word_length;
    }

    /**
     * @return string
     */
    public function getCorrectText() : ?string
    {
        return $this->correct_text;
    }

    /**
     * @return number
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @param QuestionPlayConfiguration $play
     * @return array
     */
    public static function getFields(QuestionPlayConfiguration $play) : array
    {
        global $DIC;

        self::$error_text_words = explode(' ', $play->getEditorConfiguration()->getSanitizedErrorText());

        $fields = [];
        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_wrong_text'),
            AsqTableInputFieldDefinition::TYPE_LABEL,
            self::VAR_WRONG_TEXT);

        $fields[] = new AsqTableInputFieldDefinition(
            '',
            AsqTableInputFieldDefinition::TYPE_HIDDEN,
            self::VAR_WORD_INDEX);

        $fields[] = new AsqTableInputFieldDefinition(
            '',
            AsqTableInputFieldDefinition::TYPE_HIDDEN,
            self::VAR_WORD_LENGTH);

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_correct_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_CORRECT_TEXT);

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_POINTS);

        return $fields;
    }

    /**
     * @param string $index
     * @return \srag\asq\Questions\ErrorText\ErrorTextScoringDefinition
     */
    public static function getValueFromPost(string $index) : ErrorTextScoringDefinition
    {
        return ErrorTextScoringDefinition::create(
            InputHelper::readInt(self::getPostKey($index, self::VAR_WORD_INDEX)),
            InputHelper::readInt(self::getPostKey($index, self::VAR_WORD_LENGTH)),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_CORRECT_TEXT)]),
            InputHelper::readFloat(self::getPostKey($index, self::VAR_POINTS))
        );
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Answer\Option\AnswerDefinition::getValues()
     */
    public function getValues(): array {
        return [self::VAR_WORD_INDEX => $this->wrong_word_index,
                self::VAR_WORD_LENGTH => $this->wrong_word_length,
                self::VAR_CORRECT_TEXT => $this->correct_text,
                self::VAR_POINTS => $this->points
        ];
    }
}