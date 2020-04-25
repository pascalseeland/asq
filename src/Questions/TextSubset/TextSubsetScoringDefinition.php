<?php
declare(strict_types = 1);
namespace srag\asq\Questions\TextSubset;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\InputHelper;

/**
 * Class TextSubsetScoringDefinition
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetScoringDefinition extends AnswerDefinition
{

    const VAR_TSSD_POINTS = 'tssd_points';

    const VAR_TSSD_TEXT = 'tsdd_text';

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @var ?string
     */
    protected $text;

    /**
     * TextSubsetScoringDefinition constructor.
     * @param int $points
     */
    public static function create(?float $points, ?string $text) : TextSubsetScoringDefinition
    {
        $object = new TextSubsetScoringDefinition();
        $object->points = $points;
        $object->text = $text;
        return $object;
    }

    /**
     * @return int
     */
    public function getPoints() : ?float
    {
        return $this->points;
    }

    /**
     * @return string
     */
    public function getText() : ?string
    {
        return $this->text;
    }

    /**
     * @param QuestionPlayConfiguration $play
     * @return array
     */
    public static function getFields(QuestionPlayConfiguration $play) : array
    {
        global $DIC;

        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_answer_text'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_TSSD_TEXT);

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_NUMBER,
            self::VAR_TSSD_POINTS);

        return $fields;
    }

    /**
     * @param string $index
     * @return TextSubsetScoringDefinition
     */
    public static function getValueFromPost(string $index) : TextSubsetScoringDefinition
    {
        return TextSubsetScoringDefinition::create(
            InputHelper::readFloat(self::getPostKey($index, self::VAR_TSSD_POINTS)),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_TSSD_TEXT)]));
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\Domain\Model\Answer\Option\AnswerDefinition::getValues()
     */
    public function getValues() : array
    {
        return [
            self::VAR_TSSD_POINTS => $this->points,
            self::VAR_TSSD_TEXT => $this->text
        ];
    }
}