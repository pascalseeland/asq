<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint\Form;

use ilFormPropertyGUI;
use ilHiddenInputGUI;

/**
 * Class HintFieldOrderNumber
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintFieldOrderNumber
{
    const VAR_HINT_ORDER_NUMBER = "hint_order_number";

    /**
     * HintFieldPointsDeduction constructor.
     *
     * @param float $points_deduction
     */
    public function __construct(int $order_number) {
        $this->order_number = $order_number;
    }


    public function getField(): ilFormPropertyGUI {
        $field_order_number = new ilHiddenInputGUI(self::VAR_HINT_ORDER_NUMBER);
        $field_order_number->setValue($this->order_number);

        return $field_order_number;
    }

    public  static function getValueFromPost() {
        return filter_input(INPUT_POST, self::VAR_HINT_ORDER_NUMBER);
    }
}