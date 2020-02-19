<?php

namespace ILIAS\AssessmentQuestion\Questions\ImageMap;

use ILIAS\AssessmentQuestion\DomainModel\AbstractConfiguration;
use ILIAS\AssessmentQuestion\DomainModel\Question;
use ILIAS\AssessmentQuestion\DomainModel\Answer\Answer;
use ILIAS\AssessmentQuestion\DomainModel\Answer\Option\AnswerOption;
use ILIAS\AssessmentQuestion\DomainModel\QuestionDto;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTextInputGUI;
use ILIAS\AssessmentQuestion\UserInterface\Web\ImageUploader;
use ILIAS\AssessmentQuestion\UserInterface\Web\Component\Editor\AbstractEditor;
use ILIAS\AssessmentQuestion\UserInterface\Web\Fields\AsqImageUpload;
use ilTemplate;
use Exception;
use ILIAS\AssessmentQuestion\Questions\MultipleChoice\MultipleChoiceAnswer;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class ImageMapEditor
 *
 * @package ILIAS\AssessmentQuestion\Authoring\DomainModel\Question\Answer\Option;
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class ImageMapEditor extends AbstractEditor {
    
    const VAR_IMAGE = 'ime_image';
    const VAR_MULTIPLE_CHOICE = 'ime_multiple_choice';
    const VAR_MAX_ANSWERS = 'ime_max_answers';
    const POPUP_FIELD = 'ime_popup';
    
    const STR_MULTICHOICE = 'Multichoice';
    const STR_SINGLECHOICE = 'Singlechoice';
    
    /**
     * @var ImageMapEditorConfiguration
     */
    private $configuration;
    /**
     * @var MultipleChoiceAnswer
     */
    private $selected_answers;
    
    public function __construct(QuestionDto $question) {
        $this->configuration = $question->getPlayConfiguration()->getEditorConfiguration();
        
        parent::__construct($question);
    }
    
    /**
     * @return string
     */
    public function generateHtml() : string
    {        
        $tpl = new ilTemplate("tpl.ImageMapEditor.html", true, true, "Services/AssessmentQuestion");
        
        $tpl->setCurrentBlock('generic');
        $tpl->setVariable('POST_NAME', $this->getPostName());
        $tpl->setVariable('IMAGE_URL', $this->configuration->getImage());
        $tpl->setVariable('VALUE', is_null($this->selected_answers) ? '' : implode(',', $this->selected_answers->getSelectedIds()));
        $tpl->setVariable('MAX_ANSWERS', $this->configuration->getMaxAnswers());
        $tpl->parseCurrentBlock();
        
        /** @var AnswerOption $answer_option */
        foreach ($this->question->getAnswerOptions()->getOptions() as $answer_option) {
            /** @var ImageMapEditorDisplayDefinition $display_definition */
            $display_definition = $answer_option->getDisplayDefinition();
           
            $tpl->setCurrentBlock('answer_option');
            $tpl->setVariable('OPTION_SHAPE', $this->generateShape($display_definition, $answer_option->getOptionId()));
            $tpl->parseCurrentBlock();
        }
        
        return $tpl->get();
    }
    
    private function getPostName() : string {
        return $this->question->getId();
    }
    
    /**
     * @param ImageMapEditorDisplayDefinition $display_definition
     * @param int $id
     * @return string
     */
    private function generateShape(ImageMapEditorDisplayDefinition $display_definition, int $id) : string {
        switch ($display_definition->getType()) {
            case ImageMapEditorDisplayDefinition::TYPE_CIRCLE:
                return $this->generateCircle($display_definition, $id);
            case ImageMapEditorDisplayDefinition::TYPE_POLYGON:
                return $this->generatePolygon($display_definition, $id);
            case ImageMapEditorDisplayDefinition::TYPE_RECTANGLE:
                return $this->generateRectangle($display_definition, $id);
            default:
                throw new Exception('implement rendering of shape please');
        }
    }
    
    /**
     * @param ImageMapEditorDisplayDefinition $display_definition
     * @param int $id
     * @return string
     */
    private function generateCircle(ImageMapEditorDisplayDefinition $display_definition, int $id) : string {
        $values = $this->decodeCoordinates($display_definition->getCoordinates());
        
        return '<ellipse class="' . $this->getClass($id) . '"
                      cx="' . $values['cx'] .'"
                      cy="' . $values['cy'] .'"
                      rx="' . $values['rx'] .'"
                      ry="' . $values['ry'] .'"
                      data-value="' . $id . '">
                   <title>' . $display_definition->getTooltip() . '</title>
                </ellipse>';
    }

    /**
     * @param ImageMapEditorDisplayDefinition $display_definition
     * @param int $id
     * @return string
     */
    private function generatePolygon(ImageMapEditorDisplayDefinition $display_definition, int $id) : string {
        $values = $this->decodeCoordinates($display_definition->getCoordinates());
        
        return '<polygon class="' . $this->getClass($id) . '" points="' . $values['points'] .'" data-value="' . $id . '">
                   <title>' . $display_definition->getTooltip() . '</title>
                </poligon>';
    }
    
    /**
     * @param ImageMapEditorDisplayDefinition $display_definition
     * @param int $id
     * @return string
     */
    private function generateRectangle(ImageMapEditorDisplayDefinition $display_definition, int $id) : string {
        $values = $this->decodeCoordinates($display_definition->getCoordinates());
        
        return '<rect class="' . $this->getClass($id) . '" 
                      x="' . $values['x'] .'" 
                      y="' . $values['y'] .'" 
                      width="' . $values['width'] .'" 
                      height="' . $values['height'] .'" 
                      data-value="' . $id . '">
                   <title>' . $display_definition->getTooltip() . '</title>
                </rect>';
    }
    
    /**
     * Decodes 'a:1;b:2'
     * 
     * to
     * 
     * [
     *  'a' => '1',
     *  'b' => '2'
     * ]
     * 
     * @param string $coordinates
     * @return array
     */
    private function decodeCoordinates(string $coordinates) : array {
        $raw_values = explode(';', $coordinates);
        
        $values = [];
        
        foreach ($raw_values as $raw_value) {
            $raw_split = explode(':', $raw_value);
            $values[$raw_split[0]] = $raw_split[1];
        }
        
        return $values;
    }
    
    /**
     * @param int $id
     * @return string
     */
    private function getClass(int $id) : string {
        $class = '';
        
        if (!is_null($this->selected_answers) && in_array($id, $this->selected_answers->getSelectedIds())) {
            $class .= ' selected';
        }
        
        if ($this->configuration->isMultipleChoice()) {
            $class .= ' multiple_choice';
        }
        
        return  $class;
    }
    
    /**
     * @return Answer
     */
    public function readAnswer() : AbstractValueObject
    {
        return MultipleChoiceAnswer::create(
            array_map(function($item) {
                return intval($item);
            }, explode(',', $_POST[$this->getPostName()]))
        );
    }
    
    /**
     * @param string $answer
     */
    public function setAnswer(AbstractValueObject $answer) : void
    {
        $this->selected_answers = $answer;
    }
    
    public static function generateFields(?AbstractConfiguration $config): ?array {
        /** @var ImageMapEditorConfiguration $config */
        global $DIC;
        
        $fields = [];
        
        $mode = new ilRadioGroupInputGUI($DIC->language()->txt('asq_label_mode'), self::VAR_MULTIPLE_CHOICE);
        $mode->addOption(new ilRadioOption($DIC->language()->txt('asq_label_single_choice'), self::STR_SINGLECHOICE));
        $multi = new ilRadioOption($DIC->language()->txt('asq_label_multiple_choice'), self::STR_MULTICHOICE);
        $max_answers = new ilTextInputGUI($DIC->language()->txt('asq_label_answering_limitation'), self::VAR_MAX_ANSWERS);
        $max_answers->setInfo($DIC->language()->txt('asq_info_answering_limitation'));
        $multi->addSubItem($max_answers);
        $mode->addOption($multi);
        
        
        
        
        $fields[self::VAR_MULTIPLE_CHOICE] = $mode;
        
        $image = new AsqImageUpload($DIC->language()->txt('asq_label_image'), self::VAR_IMAGE);
        $image->setRequired(true);
        $fields[self::VAR_IMAGE] = $image;
        
        $popup = new ImageFormPopup();
        $fields[self::POPUP_FIELD] = $popup;
        
        if ($config !== null) {
            $mode->setValue($config->isMultipleChoice() ? self::STR_MULTICHOICE : self::STR_SINGLECHOICE);
            $image->setImagePath($config->getImage());
            $popup->setValue($config->getImage());
            $max_answers->setValue($config->getMaxAnswers());
        }
        
        return $fields;
    }
    
    /**
     * @return AbstractConfiguration|null
     */
    public static function readConfig() : ?AbstractConfiguration {
        return ImageMapEditorConfiguration::create(
            ImageUploader::getInstance()->processImage(self::VAR_IMAGE),
            $_POST[self::VAR_MULTIPLE_CHOICE] === self::STR_MULTICHOICE,
            $_POST[self::VAR_MULTIPLE_CHOICE] === self::STR_MULTICHOICE ? 
                empty($_POST[self::VAR_MAX_ANSWERS]) ? null : intval($_POST[self::VAR_MAX_ANSWERS]) : 
                1);
    }
    
    public static function isComplete(Question $question): bool
    {
        /** @var ImageMapEditorConfiguration $config */
        $config = $question->getPlayConfiguration()->getEditorConfiguration();
        
        if (empty($config->getImage())) {
            return false;
        }
        
        foreach ($question->getAnswerOptions()->getOptions() as $option) {
            /** @var ImageMapEditorDisplayDefinition $option_config */
            $option_config = $option->getDisplayDefinition();
            
            if (empty($option_config->getType()) ||
                empty($option_config->getCoordinates()))
            {
                return false;
            }
        }
        
        return true;
    }
}