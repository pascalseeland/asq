<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Hint\Form;

use ilFormPropertyGUI;
use ilObjAdvancedEditing;

/**
 * Class HintPointsDeduction
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class HintFieldContentRte
{
    const VAR_HINT_CONTENT_RTE = "hint_content_rte";


    /**
     * HintFieldContentRte constructor.
     *
     * @param string $content
     */
    public function __construct(string $content, int $container_obj_id, string $container_obj_type) {
        $this->content = $content;
        $this->container_obj_id = $container_obj_id;
        $this->container_obj_type = $container_obj_type;
    }


    public function getField(): ilFormPropertyGUI {
        global $DIC;

        $field_content = new \ilTextAreaInputGUI($DIC->language()->txt('asq_question_hints_label_hint'), self::VAR_HINT_CONTENT_RTE);
        $field_content->setRequired(true);
        $field_content->setRows(10);
        $field_content->setUseRte(true);
        $field_content->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
        $field_content->addPlugin("latex");
        $field_content->addButton("latex");
        $field_content->addButton("pastelatex");
        $field_content->setRTESupport( $this->container_obj_id, $this->container_obj_type, "assessment");
        $field_content->setValue($this->content);


        return $field_content;
    }

    public static function getValueFromPost() {
        return filter_input(INPUT_POST, self::VAR_HINT_CONTENT_RTE);
    }
}