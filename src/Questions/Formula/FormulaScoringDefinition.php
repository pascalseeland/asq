<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

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
     * @var string
     */
    protected $formula;
    
    /**
     * @var string
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
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return float
     */
    public function getPoints()
    {
        return $this->points;
    }

    public static function getFields(QuestionPlayConfiguration $play): array
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

    public function getValues(): array
    {
        return [
            self::VAR_FORMULA => $this->formula,
            self::VAR_UNIT => $this->unit,
            self::VAR_POINTS => $this->points
        ];
    }

    public static function getValueFromPost(string $index)
    {          
        return FormulaScoringDefinition::create(
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_FORMULA)]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_UNIT)]), 
            floatval($_POST[self::getPostKey($index, self::VAR_POINTS)]));            
    }
}