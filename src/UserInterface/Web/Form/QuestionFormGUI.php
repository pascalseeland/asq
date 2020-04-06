<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form;

use Exception;
use ilDurationInputGUI;
use ilHiddenInputGUI;
use ilObjAdvancedEditing;
use ilPropertyFormGUI;
use ilSelectInputGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use ilFormSectionHeaderGUI;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\UserInterface\Web\AsqGUIElementFactory;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\PathHelper;
use srag\asq\UserInterface\Web\Form\Config\AnswerOptionForm;
use srag\CustomInputGUIs\AssessmentTest\TextInputGUI\TextInputGUI;

/**
 * Abstract Class QuestionFormGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class QuestionFormGUI extends ilPropertyFormGUI {
    const VAR_AGGREGATE_ID = 'aggregate_id';
    
    const VAR_TITLE = 'title';
    const VAR_AUTHOR = 'author';
    const VAR_DESCRIPTION = 'description';
    const VAR_QUESTION = 'question';
    const VAR_WORKING_TIME = 'working_time';
    const VAR_LIFECYCLE = 'lifecycle';
    const VAR_REVISION_NAME = 'rev_name';
    
    const VAR_LEGACY = 'legacy';
    
    const SECONDS_IN_MINUTE = 60;
    const SECONDS_IN_HOUR = 3600;
    
    const FORM_PART_LINK = 'form_part_link';
    
    const CMD_CREATE_REVISON = 'createRevision';
    
    /**
     * @var AnswerOptionForm
     */
    protected $option_form;
    
    /**
     * @var \ilLanguage
     */
    protected $lang;
    
    /**
     * @var QuestionDto
     */
    protected $initial_question;
    
    /**
     * @var QuestionDto
     */
    protected $post_question;
    
    /**
     * QuestionFormGUI constructor.
     *
     * @param QuestionDto $question
     */
    public function __construct(QuestionDto $question) {
        global $DIC;
        $this->lang = $DIC->language();
        $this->initial_question = $question;
        
        $this->initForm($question);
        $this->setMultipart(true);
        $this->setTitle(AsqGUIElementFactory::getQuestionTypes()[$question->getType()]);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->setValuesByPost();
        }
        
        parent::__construct();
    }
    
    
    /**
     * @param QuestionDto $question
     */
    private function initForm(QuestionDto $question) {
        global $DIC;
        
        $id = new ilHiddenInputGUI(self::VAR_AGGREGATE_ID);
        $id->setValue($question->getId());
        $this->addItem($id);
        
        $this->initQuestionDataConfiguration($question);
        
        if (is_null($question->getPlayConfiguration())) {
            $question->setPlayConfiguration($this->createDefaultPlayConfiguration());
        }
        
        $this->initiatePlayConfiguration($question->getPlayConfiguration());
        
        if (!is_null($question->getPlayConfiguration()) &&
            $question->getPlayConfiguration()->hasAnswerOptions() &&
            $this->canDisplayAnswerOptions())
        {
            $this->option_form = new AnswerOptionForm(
                $this->lang->txt('asq_label_answer'),
                $question->getPlayConfiguration(),
                $question->getAnswerOptions(),
                $this->getAnswerOptionDefinitions($question->getPlayConfiguration()),
                $this->getAnswerOptionConfiguration());
            
            $this->addItem($this->option_form);
        }
        
        $this->addRevisionForm();
        
        $DIC->ui()->mainTemplate()->addJavaScript(PathHelper::getBasePath(__DIR__) . 'js/AssessmentQuestionAuthoring.js');
        
        $this->postInit();
    }
    
    private function addRevisionForm() {
        global $DIC;
        
        $spacer = new ilFormSectionHeaderGUI();
        $spacer->setTitle($DIC->language()->txt('asq_version_title'));
        $this->addItem($spacer);
        
        $revision = new TextInputGUI($DIC->language()->txt('asq_label_new_revision'), self::VAR_REVISION_NAME);
        $revision->setInfo(sprintf(
            '%s<br /><input class="btn btn-default btn-sm" type="submit" name="cmd[%s]" value="%s" />',
            $DIC->language()->txt('asq_info_create_revision'),
            self::CMD_CREATE_REVISON,
            $DIC->language()->txt('asq_button_create_revision')
        ));
        $this->addItem($revision);
    }
    
    protected function getAnswerOptionConfiguration() {
        return null;
    }
    
    protected function getAnswerOptionDefinitions(?QuestionPlayConfiguration $play) : ?array {
        return null;
    }
    
    protected function canDisplayAnswerOptions() {
        return true;
    }
    
    protected function postInit() {
        //i am a virtual function :)
    }
    
    /**
     * @return QuestionDto
     */
    public function getQuestion() : QuestionDto {
        if(is_null($this->post_question)) {
            $this->post_question = $this->readQuestionFromPost();
        }
        
        return $this->post_question;
    }
    
    /**
     *
     */
    private function readQuestionFromPost()
    {
        $question = new QuestionDto();
        $question->setId($_POST[self::VAR_AGGREGATE_ID]);
        
        $question->setData($this->readQuestionData());
        
        $question->setPlayConfiguration($this->readPlayConfiguration());
        
        $question->setAnswerOptions($this->readAnswerOptions($question));
        
        $question = $this->processPostQuestion($question);
        
        return $question;
    }
    
    /**
     * @param QuestionDto $question
     * @return QuestionDto
     */
    protected function processPostQuestion(QuestionDto $question) : QuestionDto
    {
        return $question;
    }
    
    protected function readAnswerOptions(QuestionDto $question) : ?AnswerOptions {
        if (!is_null($this->option_form)) {
            $this->option_form->setConfiguration($question->getPlayConfiguration());
            $this->option_form->readAnswerOptions();
            return $this->option_form->getAnswerOptions();
        }
        
        return null;
    }
    
    
    /**
     * @param QuestionDto $question
     */
    private function initQuestionDataConfiguration(QuestionDto $question): void {
        $data = $question->getData();
        
        $title = new ilTextInputGUI($this->lang->txt('asq_label_title'), self::VAR_TITLE);
        $title->setRequired(true);
        $this->addItem($title);
        
        $author = new ilTextInputGUI($this->lang->txt('asq_label_author'), self::VAR_AUTHOR);
        $author->setRequired(true);
        $this->addItem($author);
        
        $description = new ilTextInputGUI($this->lang->txt('asq_label_description'), self::VAR_DESCRIPTION);
        $this->addItem($description);
        
        $lifecycle = new ilSelectInputGUI($this->lang->txt('asq_label_lifecycle'), self::VAR_LIFECYCLE);
        $lifecycle->setOptions([
            QuestionData::LIFECYCLE_DRAFT => $this->lang->txt('asq_lifecycle_draft'),
            QuestionData::LIFECYCLE_TO_BE_REVIEWED => $this->lang->txt('asq_lifecycle_to_be_reviewed'),
            QuestionData::LIFECYCLE_REJECTED => $this->lang->txt('asq_lifecycle_rejected'),
            QuestionData::LIFECYCLE_FINAL => $this->lang->txt('asq_lifecycle_final'),
            QuestionData::LIFECYCLE_SHARABLE => $this->lang->txt('asq_lifecycle_sharable'),
            QuestionData::LIFECYCLE_OUTDATED => $this->lang->txt('asq_lifecycle_outdated')
        ]);
        $this->addItem($lifecycle);
        
        $question_text = new ilTextAreaInputGUI($this->lang->txt('asq_label_question'), self::VAR_QUESTION);
        $question_text->setRequired(true);
        $question_text->setRows(10);
        $question_text->setUseRte(true);
        $question_text->setRteTags(ilObjAdvancedEditing::_getUsedHTMLTags("assessment"));
        $question_text->addPlugin("latex");
        $question_text->addButton("latex");
        $question_text->addButton("pastelatex");
        $this->addItem($question_text);
        
        $working_time = new ilDurationInputGUI($this->lang->txt('asq_label_working_time'), self::VAR_WORKING_TIME);
        $working_time->setShowHours(true);
        $working_time->setShowMinutes(true);
        $working_time->setShowSeconds(true);
        $this->addItem($working_time);
        
        if ($data !== null) {
            $title->setValue($data->getTitle());
            $author->setValue($data->getAuthor());
            $description->setValue($data->getDescription());
            $lifecycle->setValue($data->getLifecycle());
            $question_text->setValue($data->getQuestionText());
            $working_time->setHours(floor($data->getWorkingTime() / self::SECONDS_IN_HOUR));
            $working_time->setMinutes(floor($data->getWorkingTime() / self::SECONDS_IN_MINUTE));
            $working_time->setSeconds($data->getWorkingTime() % self::SECONDS_IN_MINUTE);
        } else {
            global $DIC;
            $author->setValue($DIC->user()->fullname);
            $working_time->setMinutes(1);
        }
    }
    
    
    /**
     * @param QuestionPlayConfiguration $play
     */
    protected abstract function initiatePlayConfiguration(?QuestionPlayConfiguration $play): void ;
        
    /**
     * @return QuestionData
     * @throws Exception
     */
    private function readQuestionData(): QuestionData {
        return QuestionData::create(
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_TITLE]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_QUESTION]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_AUTHOR]),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_DESCRIPTION]),
            $this->readWorkingTime($_POST[self::VAR_WORKING_TIME]),
            intval($_POST[self::VAR_LIFECYCLE])
            );
    }
    
    /**
     * @return QuestionPlayConfiguration
     */
    protected abstract function readPlayConfiguration(): QuestionPlayConfiguration;
    
    /**
     * @return QuestionPlayConfiguration
     */
    protected abstract function createDefaultPlayConfiguration() : QuestionPlayConfiguration;
    
    /**
     * @param $postval
     *
     * @return int
     * @throws Exception
     */
    private function readWorkingTime($postval) : int {
        $HOURS = 'hh';
        $MINUTES = 'mm';
        $SECONDS = 'ss';
        
        if (
            is_array($postval) &&
            array_key_exists($HOURS, $postval) &&
            array_key_exists($MINUTES, $postval) &&
            array_key_exists($SECONDS, $postval)) {
                return $postval[$HOURS] * self::SECONDS_IN_HOUR + $postval[$MINUTES] * self::SECONDS_IN_MINUTE + $postval[$SECONDS];
            } else {
                throw new Exception("This should be impossible, please fix implementation");
            }
    }
}