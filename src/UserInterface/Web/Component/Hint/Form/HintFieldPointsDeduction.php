<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint\Form;

use ilFormPropertyGUI;
use ilNumberInputGUI;

/**
 * Class HintFieldOrderNumber
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintFieldPointsDeduction
{
    const VAR_HINT_POINTS_DEDUCTION = "hint_points";

    /**
     * HintFieldPointsDeduction constructor.
     *
     * @param float $points_deduction
     */
    public function __construct(float $points_deduction) {
        $this->points_deduction = $points_deduction;
    }


    public function getField(): ilFormPropertyGUI {
        global $DIC;

        $field_point_deduction = new ilNumberInputGUI($DIC->language()->txt('asq_question_hints_label_points_deduction'),self::VAR_HINT_POINTS_DEDUCTION);
        $field_point_deduction->setRequired(true);
        $field_point_deduction->setSize(2);
        $field_point_deduction->setValue($this->points_deduction);

        return $field_point_deduction;
    }

    public static function getValueFromPost() {
        return filter_input(INPUT_POST, self::VAR_HINT_POINTS_DEDUCTION);
    }
}