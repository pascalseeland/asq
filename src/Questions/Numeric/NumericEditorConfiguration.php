<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class NumericEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericEditorConfiguration extends AbstractConfiguration
{
    /**
     * @var ?int
     */
    protected $max_num_of_chars;


    /**
     * @param int $max_num_of_chars
     *
     * @return NumericEditorConfiguration
     */
    public static function create(?int $max_num_of_chars = null) {
        $object = new NumericEditorConfiguration();
        $object->max_num_of_chars = $max_num_of_chars;
        return $object;
    }

    /**
     * @return int
     */
    public function getMaxNumOfChars()
    {
        return $this->max_num_of_chars;
    }

    /**
     * Compares ValueObjects to each other returns true if they are the same
     *
     * @param AbstractValueObject $other
     *
     * @return bool
     */
    function equals(AbstractValueObject $other) : bool
    {
        /** @var NumericEditorConfiguration $other */
        return get_class($this) === get_class($other) &&
               $this->max_num_of_chars === $other->max_num_of_chars;
    }
}