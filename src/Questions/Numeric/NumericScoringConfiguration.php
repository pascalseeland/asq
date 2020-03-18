<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class NumericScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericScoringConfiguration extends AbstractConfiguration {
    /**
     * @var ?int
     */
    protected $points;
    /**
     * @var ?float
     */
    protected $lower_bound;
    /**
     * @var ?float
     */
    protected $upper_bound;


    static function create(?int $points = null, ?float $lower_bound = null, ?float $upper_bound = null) : NumericScoringConfiguration
    {
        $object = new NumericScoringConfiguration();
        $object->points = $points;
        $object->lower_bound = $lower_bound;
        $object->upper_bound = $upper_bound;
        return $object;
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }


    /**
     * @return float
     */
    public function getLowerBound()
    {
        return $this->lower_bound;
    }


    /**
     * @return float
     */
    public function getUpperBound()
    {
        return $this->upper_bound;
    }

    public function equals(AbstractValueObject $other): bool
    {
        /** @var NumericScoringConfiguration $other */
        return get_class($this) === get_class($other) &&
               abs($this->lower_bound - $other->lower_bound) < PHP_FLOAT_EPSILON &&
               abs($this->upper_bound - $other->upper_bound) < PHP_FLOAT_EPSILON &&
               $this->points === $other->points;
    }
}