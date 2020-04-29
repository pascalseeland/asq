<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze;

use ilFormSectionHeaderGUI;
use ilNumberInputGUI;
use ilPropertyFormGUI;
use ilSelectInputGUI;
use ilTextAreaInputGUI;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\Domain\Model\Scoring\TextScoring;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\InputHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

/**
 * Class ClozeEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeEditor extends AbstractEditor {
    const VAR_CLOZE_TEXT = 'cze_text';
    const VAR_GAP_SIZE = 'cze_gap_size';
    const VAR_TEXT_METHOD = 'cze_text_method';
    const VAR_GAP_TYPE = 'cze_gap_type';
    const VAR_GAP_ITEMS = 'cze_gap_items';
    const VAR_GAP_VALUE = 'cze_gap_value';
    const VAR_GAP_UPPER = 'cze_gap_upper';
    const VAR_GAP_LOWER = 'cze_gap_lower';
    const VAR_GAP_POINTS = 'cze_gap_points';

    /**
     * @var ClozeEditorConfiguration
     */
    private $configuration;

    /**
     * @var AsqTableInput[]
     */
    private static $gap_tables;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question);
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::readAnswer()
     */
    public function readAnswer(): AbstractValueObject
    {
        $answers = [];

        for ($i = 1; $i <= count($this->configuration->getGaps()); $i += 1) {
            $answers[$i] = AsqHtmlPurifier::getInstance()->purify($_POST[$this->getPostVariable($i)]);
        }

        $this->answer = ClozeAnswer::create($answers);

        return $this->answer;
    }

    /**
     * @return ClozeEditorConfiguration
     */
    public static function readConfig() : ClozeEditorConfiguration
    {
        return ClozeEditorConfiguration::create(
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_CLOZE_TEXT]),
            self::readGapConfig()
        );
    }

    /**
     * @return array
     */
    public static function readGapConfig() : array
    {
        $i = 1;
        $gap_configs = [];

        while (array_key_exists($i . self::VAR_GAP_TYPE, $_POST)) {
            $istr = strval($i);

            if ($_POST[$istr . self::VAR_GAP_TYPE] == ClozeGapConfiguration::TYPE_TEXT) {
                $gap_configs[] = self::readTextGapConfiguration($istr);
            } else if ($_POST[$istr . self::VAR_GAP_TYPE] == ClozeGapConfiguration::TYPE_DROPDOWN) {
                $gap_configs[] = self::readSelectGapConfiguration($istr);
            }
            else if ($_POST[$istr . self::VAR_GAP_TYPE] == ClozeGapConfiguration::TYPE_NUMBER) {
                $gap_configs[] = self::readNumericGapConfiguration($istr);
            }
            $i += 1;
        }

        return $gap_configs;
    }

    /**
     * @param string $i
     * @return NumericGapConfiguration
     */
    private static function readNumericGapConfiguration(string $i) : NumericGapConfiguration
    {
        return NumericGapConfiguration::Create(
            InputHelper::readFloat($i . self::VAR_GAP_VALUE),
            InputHelper::readFloat($i . self::VAR_GAP_UPPER),
            InputHelper::readFloat($i . self::VAR_GAP_LOWER),
            InputHelper::readFloat($i . self::VAR_GAP_POINTS),
            InputHelper::readInt($i . self::VAR_GAP_SIZE)
        );
    }

    /**
     * @param string $i
     * @return SelectGapConfiguration
     */
    private static function readSelectGapConfiguration(string $i) : SelectGapConfiguration
    {
        return SelectGapConfiguration::Create(
            array_map(function ($raw_item) {
                return ClozeGapItem::create($raw_item[ClozeGapItem::VAR_TEXT], floatval($raw_item[ClozeGapItem::VAR_POINTS]));
            }, AsqTableInput::readValuesFromPost($i . self::VAR_GAP_ITEMS, self::getClozeGapItemFieldDefinitions()))
        );
    }

    /**
     * @param string $i
     * @return TextGapConfiguration
     */
    private static function readTextGapConfiguration(string $i) : TextGapConfiguration
    {
        return TextGapConfiguration::Create(
            array_map(function ($raw_item) {
                return ClozeGapItem::create($raw_item[ClozeGapItem::VAR_TEXT],
                                            floatval($raw_item[ClozeGapItem::VAR_POINTS]));
            }, AsqTableInput::readValuesFromPost($i . self::VAR_GAP_ITEMS, self::getClozeGapItemFieldDefinitions())),
            InputHelper::readInt($i . self::VAR_GAP_SIZE),
            InputHelper::readInt($i . self::VAR_TEXT_METHOD)
        );
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\UserInterface\Web\Component\Editor\AbstractEditor::generateHtml()
     */
    public function generateHtml(): string
    {
        $output = $this->configuration->getClozeText();

        for ($i = 1; $i <= count($this->configuration->getGaps()); $i += 1) {
            $gap_config = $this->configuration->getGaps()[$i - 1];

            if (get_class($gap_config) === SelectGapConfiguration::class) {
                $output = $this->createDropdown($i, $gap_config, $output);
            }
            else if (get_class($gap_config) === TextGapConfiguration::class) {
                $output = $this->createText($i, $gap_config, $output);
            }
            else if (get_class($gap_config) === NumericGapConfiguration::class) {
                $output = $this->createText($i, $gap_config, $output);
            }
        }

        return $output;
    }

    /**
     * @param int $index
     * @param ClozeGapConfiguration $gap_config
     * @param string $output
     * @return string
     */
    private function createDropdown(int $index, ClozeGapConfiguration $gap_config, string $output) : string
    {
        $name = '{' . $index . '}';

        $html = sprintf('<select length="20" name="%s">%s</select>',
            $this->getPostVariable($index),
            $this->createOptions($gap_config->getItems(), $index));

        return str_replace($name, $html, $output);
    }

    /**
     * @param ClozeGapItem[] $gapItems
     * @return string
     */
    private function createOptions(array $gap_items, int $index) : string
    {
        return implode(array_map(
            function(ClozeGapItem $gap_item) use ($index) {
                return sprintf('<option value="%1$s" %2$s>%1$s</option>',
                               $gap_item->getText(),
                               $gap_item->getText() === $this->getAnswer($index) ? 'selected="selected"' : '');
            },
            $gap_items
        ));
    }

    /**
     * @param int $index
     * @param ClozeGapConfiguration $gap_config
     * @param string $output
     * @return string
     */
    private function createText(int $index, ClozeGapConfiguration $gap_config, string $output) : string
    {
        $name = '{' . $index . '}';

        $html = sprintf('<input type="text" length="20" name="%s" value="%s" style="width: %spx;" />',
            $this->getPostVariable($index),
            $this->getAnswer($index) ?? '',
            $gap_config->getFieldLength());

        return str_replace($name, $html, $output);
    }

    /**
     * @param int $key
     * @return NULL|ClozeAnswer
     */
    private function getAnswer(int $key)
    {
        if (is_null($this->answer)) {
            return null;
        }

        return $this->answer->getAnswers()[$key];
    }

    /**
     * @param int $index
     * @return string
     */
    private function getPostVariable(int $index) : string
    {
        return $index . $this->question->getId();
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        if (empty($this->configuration->getClozeText())) {
            return false;
        }

        if (is_null($this->configuration->getGaps() ||
            count($this->configuration->getGaps() < 1))) {
            return false;
        }

        foreach ($this->configuration->getGaps() as $gap_config) {
            if (! $gap_config->isComplete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param AbstractConfiguration $config
     * @return array|NULL
     */
    public static function generateFields(?AbstractConfiguration $config): ?array
    {
        /** @var ClozeEditorConfiguration $config */
        global $DIC;

        self::$gap_tables = [];
        $fields = [];

        $cloze_text = new ilTextAreaInputGUI($DIC->language()->txt('asq_label_cloze_text'), self::VAR_CLOZE_TEXT);
        $cloze_text->setRequired(true);
        //TODO? template addidtion is rather hacky
        $cloze_text->setInfo($DIC->language()->txt('asq_description_cloze_text') .
                             '<br /><input type="button"
                                           value="' . $DIC->language()->txt('asq_parse_question') . '"
                                           class="js_parse_cloze_question btn btn-default" />' .
                                           self::createTemplates());
        $fields[self::VAR_CLOZE_TEXT] = $cloze_text;

        $gaps = $_SERVER['REQUEST_METHOD'] !== 'POST' ? $config->getGaps() : self::readGapConfig();

        for ($i = 1; $i <= count($gaps); $i += 1) {
            $fields = array_merge($fields, ClozeEditor::createGapFields($i, $gaps[$i - 1]));
        }

        if ($config !== null) {
            $cloze_text->setValue($config->getClozeText());
        }

        return $fields;
    }

    /**
     * @return string
     */
    private static function createTemplates() : string
    {
        return sprintf('<div class="cloze_template" style="display: none;">
                            <div class="text">%s</div>
                            <div class="number">%s</div>
                            <div class="select">%s</div>
                        </div>',
                        self::createTemplate(TextGapConfiguration::Create()),
                        self::createTemplate(NumericGapConfiguration::Create()),
                        self::createTemplate(SelectGapConfiguration::Create()));
    }

    /**
     * @param ClozeGapConfiguration $config
     * @return string
     */
    private static function createTemplate(ClozeGapConfiguration $config) : string
    {
        $fields = self::createGapFields(0, $config);

        $template_form = new ilPropertyFormGUI();

        foreach ($fields as $field) {
            $template_form->addItem($field);
        }

        return '<div class="ilFormHeader"></div>' . $template_form->getHTML();
    }

    /**
     * @param int $index
     * @param ClozeGapConfiguration $gap
     * @return array
     */
    private static function createGapFields(int $index, ClozeGapConfiguration $gap = null) : array
    {
        global $DIC;

        $fields = [];

        $spacer = new ilFormSectionHeaderGUI();
        $spacer->setTitle('');
        $fields[] = $spacer;

        $gap_type = new ilSelectInputGUI($DIC->language()->txt('asq_label_gap_type'), $index . self::VAR_GAP_TYPE);
        $gap_type->setOptions([
            ClozeGapConfiguration::TYPE_DROPDOWN => $DIC->language()->txt('asq_label_gap_type_dropdown'),
            ClozeGapConfiguration::TYPE_TEXT => $DIC->language()->txt('asq_label_gap_type_text'),
            ClozeGapConfiguration::TYPE_NUMBER => $DIC->language()->txt('asq_label_gap_type_number')
        ]);
        $gap_type->setInfo(sprintf('<a class="btn btn-default btn-sm js_delete_button">%s</a>', $DIC->language()->txt('asq_label_btn_delete_gap')));
        $fields[$index . self::VAR_GAP_TYPE] = $gap_type;

        if (!is_null($gap)) {
            if (get_class($gap) === TextGapConfiguration::class) {
                $fields = array_merge($fields, self::createTextGapFields($gap, $index));
                $gap_type->setValue(ClozeGapConfiguration::TYPE_TEXT);
            }
            else if (get_class($gap) === SelectGapConfiguration::class) {
                $fields = array_merge($fields, self::createSelectGapFields($gap, $index));
                $gap_type->setValue(ClozeGapConfiguration::TYPE_DROPDOWN);
            }
            else if (get_class($gap) === NumericGapConfiguration::class) {
                $fields = array_merge($fields, self::createNumberGapFields($gap, $index));
                $gap_type->setValue(ClozeGapConfiguration::TYPE_NUMBER);
            }
        }

        return $fields;
    }

    /**
     * @param TextGapConfiguration $gap
     * @param int $index
     * @return array
     */
    private static function createTextGapFields(TextGapConfiguration $gap, int $index) : array
    {
        global $DIC;
        $fields = [];

        $items = is_null($gap) ? [] : $gap->getItemsArray();

        $gap_items = new AsqTableInput(
            $DIC->language()->txt('asq_label_gap_items'),
            $index . self::VAR_GAP_ITEMS,
            $items,
            self::getClozeGapItemFieldDefinitions());
        $gap_items->setRequired(true);
        self::$gap_tables[$index] = $gap_items;
        $fields[$index .self::VAR_GAP_ITEMS] = $gap_items;

        $field_size = new ilNumberInputGUI(
            $DIC->language()->txt('asq_textfield_size'),
            $index . self::VAR_GAP_SIZE);
        $field_size->setValue($gap->getFieldLength());
        $fields[$index . self::VAR_GAP_SIZE] = $field_size;

        $text_method = TextScoring::getScoringTypeSelectionField($index . self::VAR_TEXT_METHOD);
        $text_method->setValue($gap->getMatchingMethod());
        $fields[$index . self::VAR_TEXT_METHOD] = $text_method;

        return $fields;
    }

    /**
     * @param SelectGapConfiguration $gap
     * @param int $index
     * @return \srag\asq\UserInterface\Web\Fields\AsqTableInput[]
     */
    private static function createSelectGapFields(SelectGapConfiguration $gap, int $index) : array
    {
        global $DIC;
        $fields = [];

        $items = is_null($gap) ? [] : $gap->getItemsArray();

        $gap_items = new AsqTableInput(
            $DIC->language()->txt('asq_label_gap_items'),
            $index . self::VAR_GAP_ITEMS,
            $items,
            self::getClozeGapItemFieldDefinitions());
        $gap_items->setRequired(true);
        self::$gap_tables[$index] = $gap_items;
        $fields[$index .self::VAR_GAP_ITEMS] = $gap_items;

        return $fields;
    }

    /**
     * @return array
     */
    private static function getClozeGapItemFieldDefinitions() : array
    {
        global $DIC;

        return [
            new AsqTableInputFieldDefinition(
                $DIC->language()->txt('asq_header_value'),
                AsqTableInputFieldDefinition::TYPE_TEXT,
                ClozeGapItem::VAR_TEXT),
            new AsqTableInputFieldDefinition(
                $DIC->language()->txt('asq_header_points'),
                AsqTableInputFieldDefinition::TYPE_TEXT,
                ClozeGapItem::VAR_POINTS)
        ];
    }

    private static function createNumberGapFields(NumericGapConfiguration $gap, int $index) {
        global $DIC;

        $fields = [];

        $value = new ilNumberInputGUI($DIC->language()->txt('asq_correct_value'), $index . self::VAR_GAP_VALUE);
        $value->setRequired(true);
        $value->allowDecimals(true);
        $value->setValue($gap->getValue());
        $fields[$index . self::VAR_GAP_VALUE] = $value;

        $upper = new ilNumberInputGUI($DIC->language()->txt('asq_label_upper_bound'), $index . self::VAR_GAP_UPPER);
        $upper->setRequired(true);
        $upper->allowDecimals(true);
        $upper->setValue($gap->getUpper());
        $fields[$index . self::VAR_GAP_UPPER]= $upper;

        $lower = new ilNumberInputGUI($DIC->language()->txt('asq_label_lower_bound'), $index . self::VAR_GAP_LOWER);
        $lower->setRequired(true);
        $lower->allowDecimals(true);
        $lower->setValue($gap->getLower());
        $fields[$index . self::VAR_GAP_LOWER] = $lower;

        $points = new ilNumberInputGUI($DIC->language()->txt('asq_header_points'), $index . self::VAR_GAP_POINTS);
        $points->setRequired(true);
        $points->setValue($gap->getPoints());
        $fields[$index . self::VAR_GAP_POINTS] = $points;

        $field_size = new ilNumberInputGUI(
            $DIC->language()->txt('asq_textfield_size'),
            $index . self::VAR_GAP_SIZE);
        $field_size->setValue($gap->getFieldLength());
        $fields[$index . self::VAR_GAP_SIZE] = $field_size;

        return $fields;
    }

    /**
     * @return string
     */
    static function getDisplayDefinitionClass() : string {
        return EmptyDefinition::class;
    }
}