<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use AsqQuestionAuthoringGUI;
use AsqQuestionConfigEditorGUI;
use AsqQuestionCreationGUI;
use AsqQuestionFeedbackEditorGUI;
use AsqQuestionHintEditorGUI;
use AsqQuestionPageGUI;
use AsqQuestionPreviewGUI;
use ILIAS\UI\Component\Link\Standard as UiStandardLink;

/**
 * Class QuestionAuthoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class LinkService
{
    public function getCreationLink(array $ctrl_stack) :UiStandardLink
    {
        global $DIC;

        array_push($ctrl_stack, AsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack, AsqQuestionCreationGUI::class);
        
        $DIC->language()->loadLanguageModule('asq');
        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_create_question_link'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack)
        );
    }

    /**
     * @return UiStandardLink
     */
    public function getEditLink(string $question_id, array $ctrl_stack = []) :UiStandardLink
    {
        global $DIC;
        
        array_push($ctrl_stack, AsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack, AsqQuestionConfigEditorGUI::class);

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_config'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack));
    }


    /**
     * @return UiStandardLink
     */
    public function getPreviewLink(string $question_id, array $ctrl_stack = []) : UiStandardLink
    {
        global $DIC;
        array_push($ctrl_stack, AsqQuestionAuthoringGUI::class);
        array_push($ctrl_stack, AsqQuestionPreviewGUI::class);

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_preview'),
            $DIC->ctrl()->getLinkTargetByClass($ctrl_stack)
        );
    }

    /**
     * @return UiStandardLink
     */
    public function getEditPageLink(string $question_id) : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_pageview'),
            $DIC->ctrl()->getLinkTargetByClass(
                [AsqQuestionAuthoringGUI::class, AsqQuestionPageGUI::class], 'edit'
            )
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getEditFeedbacksLink(string $question_id) : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_feedback'),
            $DIC->ctrl()->getLinkTargetByClass([
                AsqQuestionAuthoringGUI::class, AsqQuestionFeedbackEditorGUI::class
            ])
        );
    }


    /**
     * @return UiStandardLink
     */
    public function getEditHintsLink(string $question_id) : UiStandardLink
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        self::setQuestionUidParameter($question_id);

        return $DIC->ui()->factory()->link()->standard(
            $DIC->language()->txt('asq_authoring_tab_hints'),
            $DIC->ctrl()->getLinkTargetByClass([
                AsqQuestionAuthoringGUI::class, AsqQuestionHintEditorGUI::class
            ])
        );
    }

    /**
     * sets the question uid parameter for the ctrl hub gui ilAsqQuestionAuthoringGUI
     */
    protected function setQuestionUidParameter(string $question_id)
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        $DIC->ctrl()->setParameterByClass(
            AsqQuestionAuthoringGUI::class,
            AsqQuestionAuthoringGUI::VAR_QUESTION_ID,
            $question_id
        );
    }
}