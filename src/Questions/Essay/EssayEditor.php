<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay;

use ilNumberInputGUI;
use ilTemplate;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Class EssayEditor
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayEditor extends AbstractEditor {

    const VAR_MAX_LENGTH = "ee_max_length";

    /**
     * @var EssayEditorConfiguration
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
        global $DIC;

        $tpl = new ilTemplate(PathHelper::getBasePath(__DIR__) . 'templates/default/tpl.EssayEditor.html', true, true);

        $tpl->setVariable('ESSAY', is_null($this->answer) ? '' : $this->answer->getText());
        $tpl->setVariable('POST_VAR', $this->question->getId());

        if (!empty($this->configuration->getMaxLength())) {
            $tpl->setCurrentBlock('maximum_char_hint');
            $tpl->setVariable('MAXIMUM_CHAR_HINT', $DIC->language()->txt('asq_max_characters'));
            $tpl->setVariable('MAX_LENGTH', $this->configuration->getMaxLength());
            $tpl->setVariable('ERROR_MESSAGE', $DIC->language()->txt('asq_error_too_long'));
            $tpl->parseCurrentBlock();

            $tpl->setCurrentBlock('maxchars_counter');
            $tpl->setVariable('CHARACTERS', $DIC->language()->txt('asq_char_count'));
            $tpl->parseCurrentBlock();
        }

        // TODO wordcount??
        if (false) {
            $tpl->setCurrentBlock('maxchars_counter');
            $tpl->setVariable('CHARACTERS', $DIC->language()->txt('asq_'));
            $tpl->parseCurrentBlock();
        }

        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'src/Questions/Essay/EssayEditor.js');

        return $tpl->get();
    }

    /**
     * @return Answer
     */
    public function readAnswer() : AbstractValueObject
    {
        return EssayAnswer::create(AsqHtmlPurifier::getInstance()->purify($_POST[$this->question->getId()]));
    }

    /**
     * @param AbstractConfiguration $config
     * @return ?array
     */
    public static function generateFields(?AbstractConfiguration $config) : ?array
    {
        /** @var EssayEditorConfiguration $config */
        global $DIC;

        $fields = [];

        $max_length = new ilNumberInputGUI($DIC->language()->txt('asq_label_max_length'), self::VAR_MAX_LENGTH);
        $max_length->setSize(2);
        $max_length->setInfo($DIC->language()->txt('asq_info_max_length'));
        $fields[self::VAR_MAX_LENGTH] = $max_length;

        if (!is_null($config)) {
            $max_length->setValue($config->getMaxLength());
        }

        return $fields;
    }

    /**
     * @return ?AbstractConfiguration
     */
    public static function readConfig() : ?AbstractConfiguration
    {
        return EssayEditorConfiguration::create(intval($_POST[self::VAR_MAX_LENGTH]));
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
        // no necessary values
        return true;
    }
}