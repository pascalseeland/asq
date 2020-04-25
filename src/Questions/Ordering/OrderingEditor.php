<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Ordering;

use ilSelectInputGUI;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\Answer\Option\ImageAndTextDisplayDefinition;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;

/**
 * Class OrderingEditor
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class OrderingEditor extends AbstractEditor
{

    const VAR_VERTICAL = "oe_vertical";

    const VAR_MINIMUM_SIZE = "oe_minimum_size";

    const VERTICAL = "vertical";

    const HORICONTAL = "horicontal";

    /**
     * @var OrderingEditorConfiguration
     */
    private $configuration;

    /**
     * @var array
     */
    private $display_ids;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        $this->calculateDisplayIds($question);

        parent::__construct($question);
    }

    /**
     * @param QuestionDto $question
     */
    private function calculateDisplayIds(QuestionDto $question)
    {
        $this->display_ids = [];

        foreach ($question->getAnswerOptions()->getOptions() as $option) {
            $this->display_ids[$option->getOptionId()] = md5($question->getId() . $option->getDisplayDefinition()->getText());
        }
    }

    /**
     * @return string
     */
    public function generateHtml(): string
    {
        global $DIC;

        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.OrderingEditor.html', true, true);

        if (empty($this->answer)) {
            $items = $this->question->getAnswerOptions()->getOptions();
            shuffle($items);
        } else {
            $items = $this->orderItemsByAnswer();
        }

        foreach ($items as $item) {
            $tpl->setCurrentBlock('item');
            $tpl->setVariable('OPTION_ID', $this->display_ids[$item->getOptionId()]);
            $tpl->setVariable('ITEM_TEXT', $item->getDisplayDefinition()
                ->getText());
            $tpl->parseCurrentBlock();
        }

        $tpl->setCurrentBlock('editor');

        if (! $this->configuration->isVertical()) {
            $tpl->setVariable('ADD_CLASS', 'horizontal');
        }

        $tpl->setVariable('POST_NAME', $this->question->getId());
        $tpl->setVariable('ANSWER', $this->getAnswerString($items));
        $tpl->parseCurrentBlock();

        $DIC->ui()
            ->mainTemplate()
            ->addJavaScript(PathHelper::getBasePath(__DIR__) . 'src/Questions/Ordering/OrderingEditor.js');

        return $tpl->get();
    }

    /**
     * @param AnswerOption[] $items
     * @return string
     */
    private function getAnswerString(array $items) : string
    {
        return implode(',', array_map(function ($item) {
            return $this->display_ids[$item->getOptionId()];
        }, $items));
    }

    /**
     * @return array
     */
    private function orderItemsByAnswer() : array
    {
        $answers = $this->question->getAnswerOptions()->getOptions();

        $items = [];

        foreach ($this->answer->getSelectedOrder() as $index) {
            $items[] = $answers[$index - 1];
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::readAnswer()
     */
    public function readAnswer() : AbstractValueObject
    {
        return OrderingAnswer::create(array_map(function ($display_id) {
            return array_search($display_id, $this->display_ids);
        }, explode(',', $_POST[$this->question->getId()])));
    }

    /**
     * @param AbstractConfiguration $config
     * @return ?array
     */
    public static function generateFields(?AbstractConfiguration $config) : ?array
    {
        /** @var OrderingEditorConfiguration $config */
        global $DIC;

        $fields = [];

        $is_vertical = new ilSelectInputGUI($DIC->language()->txt('asq_label_is_vertical'), self::VAR_VERTICAL);
        $is_vertical->setOptions([
            self::VERTICAL => $DIC->language()
                ->txt('asq_label_vertical'),
            self::HORICONTAL => $DIC->language()
                ->txt('asq_label_horicontal')
        ]);
        $fields[self::VAR_VERTICAL] = $is_vertical;

        if ($config !== null) {
            $is_vertical->setValue($config->isVertical() ? self::VERTICAL : self::HORICONTAL);
        } else {
            $is_vertical->setValue(self::VERTICAL);
        }

        return $fields;
    }

    /**
     * @return OrderingEditorConfiguration
     */
    public static function readConfig() : OrderingEditorConfiguration
    {
        return OrderingEditorConfiguration::create($_POST[self::VAR_VERTICAL] === self::VERTICAL);
    }

    /**
     * @return string
     */
    static function getDisplayDefinitionClass() : string
    {
        return ImageAndTextDisplayDefinition::class;
    }

    /**
     * @param Question $question
     * @return bool
     */
    public static function isComplete(Question $question) : bool
    {
        $options = $question->getAnswerOptions()->getOptions();

        if (count($options) < 2) {
            return false;
        }

        foreach ($options as $option) {
            /** @var ImageAndTextDisplayDefinition $option_config */
            $option_config = $option->getDisplayDefinition();

            if (empty($option_config->getText())) {
                return false;
            }
        }

        return true;
    }
}