<?php
declare(strict_types=1);

namespace srag\asq\Questions\Ordering;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class OrderingEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingEditorConfiguration extends AbstractConfiguration
{
    /**
     * @var ?bool
     */
    protected $vertical;
    /**
     * @var ?int
     */
    protected $minimum_size;
    
    public static function create(
        ?bool $vertical = null, 
        ?int $minimum_size = null) : OrderingEditorConfiguration
    {
        $object = new OrderingEditorConfiguration();
        $object->vertical = $vertical;
        $object->minimum_size = $minimum_size;
        return $object;
    }
    
    /**
     * @return boolean
     */
    public function isVertical() : ?bool
    {
        return $this->vertical;
    }

    /**
     * @return int
     */
    public function getMinimumSize() : ?int
    {
        return $this->minimum_size;
    }

    public function equals(AbstractValueObject $other): bool
    {
        /** @var OrderingEditorConfiguration $other */
        return get_class($this) === get_class($other) &&
               $this->isVertical() === $other->isVertical() &&
               $this->getMinimumSize() === $other->getMinimumSize();
    }
}