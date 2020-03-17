<?php

namespace ILIAS\AssessmentQuestion\UserInterface\Web;

/**
 * Class PathHelper
 *
 * @package ILIAS\AssessmentQuestion\Authoring\DomainModel\Question\Answer\Option;
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class PathHelper {
    public static function getBasePath(string $fullpath) : string {
        $dir = substr($fullpath, strpos($fullpath, "/Customizing/") + 1);
        return substr($dir, 0, strpos($dir, "/src/") + 1);
    }
}