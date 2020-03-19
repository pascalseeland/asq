<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use ilPropertyFormGUI;
use ilSelectInputGUI;
use srag\asq\Domain\Model\ContentEditingMode;
use srag\asq\UserInterface\Web\AsqGUIElementFactory;

/**
 * Class QuestionTypeSelectForm
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionTypeSelectForm extends ilPropertyFormGUI {
	const VAR_QUESTION_TYPE = "question_type";
	const VAR_CONTENT_EDIT_MODE = "content_edit_mode";

    /**
     * QuestionTypeSelectForm constructor.
     */
	public function __construct( ) {
		$this->initForm();

		parent::__construct();
	}

	/**
	 * Init settings property form
	 *
	 * @access private
	 */
	private function initForm() {

	    global $DIC; /* @var \ILIAS\DI\Container $DIC */

	    $this->setTitle($DIC->language()->txt('asq_create_question_form'));

		$select = new ilSelectInputGUI(
		    $DIC->language()->txt('asq_input_question_type'), self::VAR_QUESTION_TYPE
        );
		$select->setOptions(AsqGUIElementFactory::getQuestionTypes());
		$this->addItem($select);

        if( \ilObjAssessmentFolder::isAdditionalQuestionContentEditingModePageObjectEnabled() )
        {
            $radio = new \ilRadioGroupInputGUI(
                $DIC->language()->txt("asq_input_cont_edit_mode"), self::VAR_CONTENT_EDIT_MODE
            );

            $radio->addOption(new \ilRadioOption(
                $DIC->language()->txt('asq_input_cont_edit_mode_rte_textarea'),
                ContentEditingMode::RTE_TEXTAREA
            ));

            $radio->addOption(new \ilRadioOption(
                $DIC->language()->txt('asq_input_cont_edit_mode_page_object'),
                ContentEditingMode::PAGE_OBJECT
            ));

            $radio->setValue(ContentEditingMode::RTE_TEXTAREA);

            $this->addItem($radio);
        }
	}

    /**
     * @return int|null
     */
	public function getQuestionType() : ?int {
		return intval($_POST[self::VAR_QUESTION_TYPE]);
	}


    /**
     * @return bool
     */
	public function hasContentEditingMode() : bool
    {
        $input = $this->getItemByPostVar(self::VAR_CONTENT_EDIT_MODE);
        return $input instanceof \ilFormPropertyGUI;
    }


    /**
     * @return bool
     */
    public function getContentEditingMode() : string
    {
        return $this->hasContentEditingMode() ?  $this->getInput(self::VAR_CONTENT_EDIT_MODE) : ContentEditingMode::RTE_TEXTAREA;
    }
}
