<?php
declare(strict_types=1);

namespace srag\asq\Questions\Kprim;

use ilCheckboxInputGUI;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTemplate;
use ilTextInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Feedback;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\InputHelper;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class KprimChoiceEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceEditor extends AbstractEditor {
    const VAR_SHUFFLE_ANSWERS = 'kce_shuffle';
    const VAR_SINGLE_LINE = 'kce_single_line';
    const VAR_THUMBNAIL_SIZE = 'kce_thumbnail';
    const VAR_LABEL_TYPE = 'kcd_label';
    const VAR_LABEL_TRUE = 'kce_label_true';
    const VAR_LABEL_FALSE = 'kce_label_false';

    const STR_TRUE = "True";
    const STR_FALSE = "False";

    const LABEL_RIGHT_WRONG = "label_rw";
    const LABEL_PLUS_MINUS = "label_pm";
    const LABEL_APPLICABLE = "label_app";
    const LABEL_ADEQUATE = "label_aed";
    const LABEL_CUSTOM = "label_custom";

    const STR_RIGHT = 'right';
    const STR_WRONG = 'wrong';
    const STR_PLUS = '+';
    const STR_MINUS = '-';
    const STR_APPLICABLE = 'applicable';
    const STR_NOT_APPLICABLE = 'not applicable';
    const STR_ADEQUATE = 'adequate';
    const STR_NOT_ADEQUATE = 'not adequate';

    /**
     * @var array
     */
    private $answer_options;
    /**
     * @var KprimChoiceEditorConfiguration
     */
    private $configuration;

    public function __construct(QuestionDto $question)
    {
        $this->answer_options = $question->getAnswerOptions()->getOptions();
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        if ($this->configuration->isShuffleAnswers()) {
            shuffle($this->answer_options);
        }

        parent::__construct($question);
    }

    public function readAnswer(): AbstractValueObject
    {
        $answers = [];

        /** @var AnswerOption $answer_option */
        foreach ($this->answer_options as $answer_option) {
            $answer = $_POST[$this->getPostName($answer_option->getOptionId())];


            if ($answer === self::STR_TRUE) {
                $answers[$answer_option->getOptionId()] = true;
            }
            else if ($answer === self::STR_FALSE) {
                $answers[$answer_option->getOptionId()] = false;
            }
            else {
                $answers[$answer_option->getOptionId()] = null;
            }
        }

        return KprimChoiceAnswer::create($answers);
    }

    /**
     * @return string
     */
    public function generateHtml(): string
    {
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.KprimChoiceEditor.html', true, true);

        $tpl->setCurrentBlock('header');
        $tpl->setVariable('INSTRUCTIONTEXT', "You have to decide on every statement: [{$this->configuration->getLabelTrue()}] or [{$this->configuration->getLabelFalse()}]");
        $tpl->setVariable('OPTION_LABEL_TRUE', $this->configuration->getLabelTrue());
        $tpl->setVariable('OPTION_LABEL_FALSE', $this->configuration->getLabelFalse());
        $tpl->parseCurrentBlock();

        /** @var AnswerOption $answer_option */
        foreach ($this->answer_options as $answer_option) {
            /** @var ImageAndTextDisplayDefinition $display_definition */
            $display_definition = $answer_option->getDisplayDefinition();

            if (!empty($display_definition->getImage())) {
                $tpl->setCurrentBlock('answer_image');
                $tpl->setVariable('ANSWER_IMAGE_URL', $display_definition->getImage());
                $tpl->setVariable('ANSWER_IMAGE_ALT', $display_definition->getText());
                $tpl->setVariable('ANSWER_IMAGE_TITLE', $display_definition->getText());
                $tpl->setVariable('THUMB_SIZE',
                    is_null($this->configuration->getThumbnailSize()) ?
                    '' :
                    sprintf(' style="height: %spx;" ', $this->configuration->getThumbnailSize()));
                $tpl->parseCurrentBlock();
            }

            if ($this->render_feedback
                && !is_null($this->answer)
                && !is_null($this->question->getFeedback())
                && !is_null($this->question->getFeedback()->getFeedbackForAnswerOption($answer_option->getOptionId()))
                && $this->showFeedbackForAnswerOption($answer_option))
            {
                $tpl->setCurrentBlock('feedback');
                $tpl->setVariable('FEEDBACK', $this->question->getFeedback()->getFeedbackForAnswerOption($answer_option->getOptionId()));
                $tpl->parseCurrentBlock();
            }

            $tpl->setCurrentBlock('answer_row');
            $tpl->setVariable('ANSWER_TEXT', $display_definition->getText());
            $tpl->setVariable('ANSWER_ID', $this->getPostName($answer_option->getOptionId()));
            $tpl->setVariable('VALUE_TRUE', self::STR_TRUE);
            $tpl->setVariable('VALUE_FALSE', self::STR_FALSE);

            if (!is_null($this->answer)) {
                $answer = $this->answer->getAnswerForId($answer_option->getOptionId());
                if($answer === true) {
                    $tpl->setVariable('CHECKED_ANSWER_TRUE', 'checked="checked"');
                }
                else if ($answer === false) {
                    $tpl->setVariable('CHECKED_ANSWER_FALSE', 'checked="checked"');
                }
            }

            $tpl->parseCurrentBlock();
        }

        return $tpl->get();
    }

    private function showFeedBackForAnswerOption(AnswerOption $option) : bool
    {
        switch ($this->question->getFeedback()->getAnswerOptionFeedbackMode()) {
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_ALL:
                return true;
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CHECKED:
                return $this->answer->getAnswerForId($option->getOptionId());
            case Feedback::OPT_ANSWER_OPTION_FEEDBACK_MODE_CORRECT:
                return $this->answer->getAnswerForId($option->getOptionId()) === $option->getScoringDefinition()->isCorrectValue();
            default:
                return false;
        }
    }

    /**
     * @param string $id
     * @return string
     */
    private function getPostName(string $id)
    {
        return $this->question->getId() . $id;
    }

    /**
     *
     * @param AbstractConfiguration $config
     * @return array|NULL
     */
    public static function generateFields(?AbstractConfiguration $config): ?array
    {
        /** @var KprimChoiceEditorConfiguration $config */
        global $DIC;

        $fields = [];

        $shuffle = new ilCheckboxInputGUI($DIC->language()->txt('asq_label_shuffle'), self::VAR_SHUFFLE_ANSWERS);
        $shuffle->setValue(self::STR_TRUE);
        $fields[self::VAR_SHUFFLE_ANSWERS] = $shuffle;

        $thumb_size = new ilNumberInputGUI(
            $DIC->language()->txt('asq_label_thumb_size'),
            self::VAR_THUMBNAIL_SIZE);
        $thumb_size->setInfo($DIC->language()->txt('asq_description_thumb_size'));
        $thumb_size->setSuffix($DIC->language()->txt('asq_pixel'));
        $thumb_size->setMinValue(20);
        $thumb_size->setDecimals(0);
        $thumb_size->setSize(6);
        $fields[self::VAR_THUMBNAIL_SIZE] = $thumb_size;

        $optionLabel = KprimChoiceEditor::GenerateOptionLabelField($config);
        $fields[self::VAR_LABEL_TYPE] = $optionLabel;

        if ($config !== null) {
            $shuffle->setChecked($config->isShuffleAnswers());
            $thumb_size->setValue($config->getThumbnailSize());
        }
        else {
            $shuffle->setChecked(true);
        }

        return $fields;
    }

    /**
     * public due to use in legacy form
     * @param AbstractConfiguration $config
     * @return \ilRadioGroupInputGUI
     */
    public static function GenerateOptionLabelField(?AbstractConfiguration $config)
    {
        /** @var KprimChoiceEditorConfiguration $config */
        global $DIC;

        $optionLabel = new ilRadioGroupInputGUI(
            $DIC->language()->txt('asq_label_obtion_labels'),
            self::VAR_LABEL_TYPE);
        $optionLabel->setInfo($DIC->language()->txt('asq_description_options'));
        $optionLabel->setRequired(true);

        $right_wrong = new ilRadioOption(
            $DIC->language()->txt('asq_label_right_wrong'),
            self::LABEL_RIGHT_WRONG);
        $optionLabel->addOption($right_wrong);

        $plus_minus = new ilRadioOption(
            $DIC->language()->txt('asq_label_plus_minus'),
            self::LABEL_PLUS_MINUS);
        $optionLabel->addOption($plus_minus);

        $applicable = new ilRadioOption(
            $DIC->language()->txt('asq_label_applicable'),
            self::LABEL_APPLICABLE);
        $optionLabel->addOption($applicable);

        $adequate = new ilRadioOption(
            $DIC->language()->txt('asq_label_adequate'),
            self::LABEL_ADEQUATE);
        $optionLabel->addOption($adequate);

        $custom = new ilRadioOption(
            $DIC->language()->txt('asq_label_userdefined'),
            self::LABEL_CUSTOM);
        $optionLabel->addOption($custom);

        $customLabelTrue = new ilTextInputGUI(
            $DIC->language()->txt('asq_label_user_true'),
            self::VAR_LABEL_TRUE);
        $custom->addSubItem($customLabelTrue);

        $customLabelFalse = new ilTextInputGUI(
            $DIC->language()->txt('asq_label_user_false'),
            self::VAR_LABEL_FALSE);
        $custom->addSubItem($customLabelFalse);

        if ($config !== null) {
            if($config->getLabelTrue() === self::STR_RIGHT && $config->getLabelFalse() === self::STR_WRONG) {
                $optionLabel->setValue(self::LABEL_RIGHT_WRONG);
            }
            else if ($config->getLabelTrue() === self::STR_PLUS && $config->getLabelFalse() === self::STR_MINUS) {
                $optionLabel->setValue(self::LABEL_PLUS_MINUS);
            }
            else if ($config->getLabelTrue() === self::STR_APPLICABLE && $config->getLabelFalse() === self::STR_NOT_APPLICABLE) {
                $optionLabel->setValue(self::LABEL_APPLICABLE);
            }
            else if ($config->getLabelTrue() === self::STR_ADEQUATE && $config->getLabelFalse() === self::STR_NOT_ADEQUATE) {
                $optionLabel->setValue(self::LABEL_ADEQUATE);
            } else if (empty($config->getLabelTrue())) {
                $optionLabel->setValue(self::LABEL_RIGHT_WRONG);
            }
            else {
                $optionLabel->setValue(self::LABEL_CUSTOM);
                $customLabelTrue->setValue($config->getLabelTrue());
                $customLabelFalse->setValue($config->getLabelFalse());
            }
        }

        return $optionLabel;
    }

    /**
     * @return ?AbstractConfiguration|null
     */
    public static function readConfig() : ?AbstractConfiguration {
        switch ($_POST[self::VAR_LABEL_TYPE]) {
            case self::LABEL_RIGHT_WRONG:
                $label_true = self::STR_RIGHT;
                $label_false = self::STR_WRONG;
                break;
            case self::LABEL_PLUS_MINUS:
                $label_true = self::STR_PLUS;
                $label_false = self::STR_MINUS;
                break;
            case self::LABEL_APPLICABLE:
                $label_true = self::STR_APPLICABLE;
                $label_false = self::STR_NOT_APPLICABLE;
                break;
            case self::LABEL_ADEQUATE:
                $label_true = self::STR_ADEQUATE;
                $label_false = self::STR_NOT_ADEQUATE;
                break;
            case self::LABEL_CUSTOM:
                $label_true = AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_LABEL_TRUE]);
                $label_false = AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_LABEL_FALSE]);
                break;
        }

        $thumbsize = InputHelper::readInt(self::VAR_THUMBNAIL_SIZE);

        return KprimChoiceEditorConfiguration::create(
            $_POST[self::VAR_SHUFFLE_ANSWERS] === self::STR_TRUE,
            $_POST[self::VAR_SINGLE_LINE] === self::STR_TRUE,
            $thumbsize,
            $label_true,
            $label_false);
    }

    /**
     * @return string
     */
    static function getDisplayDefinitionClass() : string {
        return ImageAndTextDisplayDefinition::class;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (empty($this->configuration->getLabelFalse()) ||
            empty($this->configuration->getLabelTrue()))
        {
            return false;
        }

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ImageAndTextDisplayDefinition $option_config */
            $option_config = $option->getDisplayDefinition();

            if (empty($option_config->getText()))
            {
                return false;
            }
        }

        return true;
    }
}