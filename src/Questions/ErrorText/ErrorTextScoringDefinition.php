<?php
declare(strict_types=1);

namespace srag\asq\Questions\ErrorText;


use stdClass;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

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
     * @var int
     */
    protected $wrong_word_index;
    /**
     * @var int
     */
    protected $wrong_word_length;
    /**
     * @var string
     */
    protected $correct_text;
    /**
     * @var float
     */
    protected $points;
    
    /**
     * @var array
     */
    private static $error_text_words;
    
    /**
     * ErrorTextScoringDefinition constructor.
     *
     * @param int $points
     */
    public function __construct(int $wrong_word_index, int $wrong_word_length, ?string $correct_text, float $points)
    {
        $this->wrong_word_index = $wrong_word_index;
        $this->wrong_word_length = $wrong_word_length;
        $this->correct_text = $correct_text;
        $this->points = $points;
    }
    
    /**
     * @return int
     */
    public function getWrongWordIndex()
    {
        return $this->wrong_word_index;
    }
    
    /**
     * @return int
     */
    public function getWrongWordLength()
    {
        return $this->wrong_word_length;
    }
    
    /**
     * @return string
     */
    public function getCorrectText()
    {
        return $this->correct_text;
    }
    
    /**
     * @return number
     */
    public function getPoints()
    {
        return $this->points;
    }
    
    public static function getFields(QuestionPlayConfiguration $play): array {
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
    
    private static function storeErrorText(?string $error_text) {
        if (is_null($error_text)) {
            return;
        }
        
        $error_text = str_replace('#', '', $error_text);
        $error_text = str_replace('((', '', $error_text);
        $error_text = str_replace('))', '', $error_text);
        self::$error_text_words = explode(' ', $error_text);
    }
    
    public static function getValueFromPost(string $index) {
        return new ErrorTextScoringDefinition(
            intval($_POST[self::getPostKey($index, self::VAR_WORD_INDEX)]),
            intval($_POST[self::getPostKey($index, self::VAR_WORD_LENGTH)]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_CORRECT_TEXT)]),
            floatval($_POST[self::getPostKey($index, self::VAR_POINTS)]));
    }
    
    public function getValues(): array {
        return [self::VAR_WRONG_TEXT => $this->calculateErrorText($this->wrong_word_index, 
                                                                  $this->wrong_word_length),
                self::VAR_WORD_INDEX => $this->wrong_word_index,
                self::VAR_WORD_LENGTH => $this->wrong_word_length,
                self::VAR_CORRECT_TEXT => $this->correct_text,
                self::VAR_POINTS => $this->points
        ];
    }
    
    private function calculateErrorText(int $index, int $length) {
        $text = '';
        
        for ($i = $index; $i < $index + $length; $i++) {
            $text .= self::$error_text_words[$i] . ' ';
        }
        
        return $text;
    }
    
    
    public static function deserialize(stdClass $data) {
        return new ErrorTextScoringDefinition(
            $data->wrong_word_index,
            $data->wrong_word_length,
            $data->correct_text,
            $data->points);
    }
}