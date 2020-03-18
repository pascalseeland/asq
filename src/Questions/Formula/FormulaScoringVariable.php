<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class FormulaScoringVariable
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaScoringVariable extends AbstractValueObject {
    const VAR_MIN = 'fsv_min';
    const VAR_MAX = 'fsv_max';
    const VAR_UNIT = 'fsv_unit';
    const VAR_MULTIPLE_OF = 'fsv_multiple_of';

    /**
     * @var float
     */
    protected $min;
    
    /**
     * @var float
     */
    protected $max;
    
    /**
     * @var string
     */
    protected $unit;
    
    /**
     * @var ?float
     */
    protected $multiple_of;
    
    /**
     * @param float $min
     * @param float $max
     * @param string $unit
     * @param float $divisor
     * @return FormulaScoringVariable
     */
    public static function create(float $min,
                                  float $max,
                                  string $unit,
                                  ?float $multiple_of) : 
                                  FormulaScoringVariable {
        $object = new FormulaScoringVariable();
        $object->min = $min;
        $object->max = $max;
        $object->unit = $unit;
        $object->multiple_of = $multiple_of;
        return $object;
    }
    
    /**
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return float
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return ?float
     */
    public function getMultipleOf()
    {
        return $this->multiple_of;
    }

    public function getAsArray(): array {
        return [
            self::VAR_MIN => $this->min,
            self::VAR_MAX => $this->max,
            self::VAR_UNIT => $this->unit,
            self::VAR_MULTIPLE_OF => $this->multiple_of
        ];
    }
}