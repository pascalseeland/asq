<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint\Form;

use ILIAS\UI\Component\Layout\Page\Page;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Hint\Hint;

/**
 * Class HintFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintFormGUI extends \ilPropertyFormGUI
{
    /**
     * @var Page
     */
    protected $page;
    /**
     * @var QuestionDto
     */
    protected $question_dto;
    /**
     * @var Hint
     */
    protected $hint;


    /**
     * QuestionHintFormGUI constructor.
     *
     * @param Page        $page
     * @param QuestionDto $questionDto
     */
    public function __construct(
        QuestionDto $question_dto,
        Hint $hint
    ) {
        global $DIC;
        /* @var \ILIAS\DI\Container $DIC */

        parent::__construct();

        $this->question_dto = $question_dto;
        $this->hint = $hint;

        $this->setTitle($DIC->language()->txt('asq_feedback_form_title'));

        $this->initForm();
    }


    protected function initForm()
    {
        global $DIC;
        /* @var \ILIAS\DI\Container $DIC */

        $this->setTitle(sprintf($DIC->language()->txt('asq_question_hints_form_header'), $this->question_dto->getData()->getTitle()));

        //Hint Order Number
        $order_number = new HintFieldOrderNumber($this->hint->getOrderNumber());
        $this->addItem($order_number->getField());

        //RTE or PageEditor?
        $content_rte = new HintFieldContentRte($this->hint->getContent(), $this->question_dto->getContainerObjId(), $this->question_dto->getLegacyData()->getContainerObjType());
        $this->addItem($content_rte->getField());

        $points_deduction = new HintFieldPointsDeduction($this->hint->getPointDeduction());
        $this->addItem($points_deduction->getField());
    }

    public static function getHintFromPost() {

        $hint_order_number =  HintFieldOrderNumber::getValueFromPost();
        $content_rte = HintFieldContentRte::getValueFromPost();
        $points_deduction = HintFieldPointsDeduction::getValueFromPost();

        return new Hint($hint_order_number, $content_rte,$points_deduction);
    }
}