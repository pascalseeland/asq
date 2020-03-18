<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint\Table;

/**
 * Class HintTableFieldOrderNumber
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintTableFieldSelectHint
{
    const VAR_HINTS_BY_ORDER_NUMBER = "hints_by_number";

    /**
     * HintFieldPointsDeduction constructor.
     *
     * @param float $points_deduction
     */
    public function __construct(int $order_number) {
        $this->order_number = $order_number;
    }


    public function getFieldAsHtml(): string {
        $field_select_hint = '<input type="checkbox" name="'.self::VAR_HINTS_BY_ORDER_NUMBER.'[]" value="'.$this->order_number.'" id="chb_'. $this->order_number.'" />';

        return $field_select_hint;
    }

    public static function getValueFromPost() {
        return filter_input(INPUT_POST, self::VAR_HINTS_BY_ORDER_NUMBER, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
    }
}