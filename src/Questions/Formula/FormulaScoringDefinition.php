<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\InputHelper;

/**
 * Class FormulaScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaScoringDefinition extends AnswerDefinition {
    const VAR_FORMULA = 'fsd_formula';
    const VAR_UNIT = 'fsd_unit';
    const VAR_POINTS = 'fsd_points';

    /**
     * @var ?string
     */
    protected $formula;

    /**
     * @var ?string
     */
    protected $unit;

    /**
     * @var ?float
     */
    protected $points;

    /**
     * @param string $formula
     * @param string $unit
     * @param float $points
     * @return FormulaScoringDefinition
     */
    public static function create(string $formula, string $unit, float $points) : FormulaScoringDefinition {
        $object = new FormulaScoringDefinition();
        $object->formula = $formula;
        $object->unit = $unit;
        $object->points = $points;
        return $object;
    }

    /**
     * @return ?string
     */
    public function getFormula() : ?string
    {
        return $this->formula;
    }

    /**
     * @return ?string
     */
    public function getUnit() : ?string
    {
        return $this->unit;
    }

    /**
     * @return ?float
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

        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_header_formula'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_FORMULA);

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_header_unit'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_UNIT);

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_points'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_POINTS);

        return $fields;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Answer\Option\AnswerDefinition::getValues()
     */
    public function getValues() : array
    {
        return [
            self::VAR_FORMULA => $this->formula,
            self::VAR_UNIT => $this->unit,
            self::VAR_POINTS => $this->points
        ];
    }

    /**
     * @param string $index
     * @return FormulaScoringDefinition
     */
    public static function getValueFromPost(string $index) : FormulaScoringDefinition
    {
        return FormulaScoringDefinition::create(
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_FORMULA)]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_UNIT)]),
            InputHelper::readFloat(self::getPostKey($index, self::VAR_POINTS)));
    }

    /**
     * @param array $units
     * @return bool
     */
    public function isComplete(FormulaScoringConfiguration $config) : bool
    {
        if (is_null($this->getPoints())) {
            return false;
        }

        if (! is_null($this->getUnit()) &&
            ! in_array($this->getUnit(), $config->getUnits())) {
            return false;
        }

        return true;
    }
}