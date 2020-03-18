<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web;

/**
 * Class PathHelper
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class PathHelper {
    public static function getBasePath(string $fullpath) : string {
        $dir = substr($fullpath, strpos($fullpath, "/Customizing/") + 1);
        
        if (strpos($dir, "/src")) {
            return substr($dir, 0, strpos($dir, "/src") + 1);
        }
        else {
            return substr($dir, 0, strpos($dir, "/classes") + 1);
        }
    }
}