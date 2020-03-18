<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class FormulaScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaScoringConfiguration extends AbstractConfiguration {    
    /**
     * @var string
     */
    protected $units;
    
    /**
     * @var int
     */
    protected $precision;
    
    /**
     * @var float
     */
    protected $tolerance;
    
    /**
     * @var int
     */
    protected $result_type;
    
    /**
     * @var FormulaScoringVariable[]
     */
    protected $variables = [];
    
    const TYPE_ALL = 1;
    const TYPE_DECIMAL = 2;
    const TYPE_FRACTION = 3;
    const TYPE_COPRIME_FRACTION = 4;
    
    public static function create(string $units = null,
                                  int $precision = null,
                                  float $tolerance = null,
                                  int $result_type = null,
                                  array $variables = []) : FormulaScoringConfiguration {
        
        $object = new FormulaScoringConfiguration();
        
        $object->units = $units;
        $object->precision = $precision;
        $object->tolerance = $tolerance;
        $object->result_type = $result_type;
        $object->variables = $variables;
        
        return $object;
    }

    /**
     * @return string
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return float
     */
    public function getTolerance()
    {
        return $this->tolerance;
    }

    /**
     * @return int
     */
    public function getResultType()
    {
        return $this->result_type;
    }
    
    public function getVariables() {
        return $this->variables;
    }

    /**
     * @return array
     */
    public function getVariablesArray(): array {
        $var_array = [];
        
        foreach($this->variables as $variable) {
            $var_array[] = $variable->getAsArray();
        }
        
        return $var_array;
    }
}