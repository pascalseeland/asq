<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web;

/**
 * Class InputHelper
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class InputHelper {
    /**
     * @param string $postvar
     * @return float|NULL
     */
    public static function readFloat(string $postvar) : ?float 
    {
        if (! array_key_exists($postvar, $_POST) ||
            ! is_numeric($_POST[$postvar])) 
        {
            return null;
        }
        
        return floatval($_POST[$postvar]);
    }
    
    /**
     * @param string $postvar
     * @return int|NULL
     */
    public static function readInt(string $postvar) : ?int
    {
        if (! array_key_exists($postvar, $_POST) ||
            ! is_numeric($_POST[$postvar])) 
        {
            return null;
        }
        
        return intval($_POST[$postvar]);
    }
}