<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze;

/**
 * Class SelectGapConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SelectGapConfiguration extends ClozeGapConfiguration {
    /**
     * @var ?ClozeGapItem[]
     */
    protected $items;
    
    public static function Create(array $items = []) {
        $object = new SelectGapConfiguration();
        $object->items = $items;
        return $object;
    }
    
    /**
     * @return ?array
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * @return array
     */
    public function getItemsArray(): array {
        $var_array = [];
        
        if (!is_null($this->items)) {
            foreach($this->items as $variable) {
                $var_array[] = $variable->getAsArray();
            }
        }
        
        return $var_array;
    }
    
    public function getMaxPoints(): float
    {
        $gap_max = 0;
        
        /** @var $gap ClozeGapItem */
        foreach($this->items as $gap_item) {
            if ($gap_item->getPoints() > $gap_max) {
                $gap_max = $gap_item->getPoints();
            }
        }
        
        return $gap_max;
    }
}