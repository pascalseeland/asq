<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric;

use ilNumberInputGUI;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\UserInterface\Web\InputHelper;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class NumericEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericEditor extends AbstractEditor {

    const VAR_MAX_NR_OF_CHARS = 'ne_max_nr_of_chars';

    /**
     * @var NumericEditorConfiguration
     */
    private $configuration;

    /**
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question)
    {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();

        parent::__construct($question);
    }

    /**
     * @return string
     */
    public function generateHtml() : string
    {
        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.NumericEditor.html', true, true);

        $tpl->setCurrentBlock('editor');
        $tpl->setVariable('POST_NAME', $this->question->getId());
        $tpl->setVariable('NUMERIC_SIZE', $this->configuration->getMaxNumOfChars());

        if ($this->answer !== null) {
            $tpl->setVariable('CURRENT_VALUE', 'value="' . $this->answer->getValue() . '"');
        }

        $tpl->parseCurrentBlock();

        return $tpl->get();
    }

    /**
     * @return Answer
     */
    public function readAnswer() : AbstractValueObject
    {
        return NumericAnswer::create(floatval($_POST[$this->question->getId()]));
    }

    /**
     * @param AbstractConfiguration $config
     * @return array|NULL
     */
    public static function generateFields(?AbstractConfiguration $config) : ?array
    {
        /** @var NumericEditorConfiguration $config */
        global $DIC;

        $fields = [];

        $max_chars = new ilNumberInputGUI($DIC->language()->txt('asq_label_max_nr_of_chars'), self::VAR_MAX_NR_OF_CHARS);
        $max_chars->setInfo($DIC->language()->txt('asq_description_max_nr_chars'));
        $max_chars->setSize(6);
        $fields[self::VAR_MAX_NR_OF_CHARS] = $max_chars;

        if ($config !== null) {
            $max_chars->setValue($config->getMaxNumOfChars());
        }

        return $fields;
    }

    /**
     * @return AbstractConfiguration|null
     */
    public static function readConfig() : ?AbstractConfiguration
    {
        return NumericEditorConfiguration::create(InputHelper::readInt(self::VAR_MAX_NR_OF_CHARS));
    }

    /**
     * @return string
     */
    static function getDisplayDefinitionClass() : string
    {
        return EmptyDefinition::class;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        //numeric editor always works
        return true;
    }
}