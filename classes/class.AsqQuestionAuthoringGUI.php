<?php
declare(strict_types=1);

use srag\CQRS\Aggregate\DomainObjectId;
use srag\asq\AsqGateway;
use srag\asq\Application\Service\AuthoringContextContainer;

/**
 * Class AsqQuestionAuthoringGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionCreationGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionPreviewGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionPageGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionConfigEditorGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionFeedbackEditorGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: AsqQuestionHintEditorGUI
 * @ilCtrl_Calls AsqQuestionAuthoringGUI: ilCommonActionDispatcherGUI
 */
class AsqQuestionAuthoringGUI
{
    const TAB_ID_PREVIEW = 'qst_preview_tab';
    const TAB_ID_PAGEVIEW = 'qst_pageview_tab';
    const TAB_ID_CONFIG = 'qst_config_tab';
    const TAB_ID_FEEDBACK = 'qst_feedback_tab';
    const TAB_ID_HINTS = 'qst_hints_tab';
    const TAB_ID_RECAPITULATION = 'qst_recapitulation_tab';
    const TAB_ID_STATISTIC = 'qst_statistic_tab';

    const VAR_QUESTION_ID = "question_id";

    const CMD_REDRAW_HEADER_ACTION_ASYNC = '';

    /**
     * @var AuthoringContextContainer
     */
	protected $authoring_context_container;
    /**
     * @var DomainObjectId
     */
    protected $question_id;
    /**
     * @var string
     */
    protected $lng_key;

    /**
     * ilAsqQuestionAuthoringGUI constructor.
     *
     * @param AuthoringContextContainer $authoring_context_container
     */
	function __construct(AuthoringContextContainer $authoring_context_container)
	{
	    global $DIC; /* @var ILIAS\DI\Container $DIC */

	    $this->authoring_context_container = $authoring_context_container;

	    //we could use this in future in constructer
	    $this->lng_key = $DIC->language()->getDefaultLanguage();

        $this->question_id = $this->currentOrNewQuestionId();
        
        $DIC->language()->loadLanguageModule('asq');
    }

    /**
     * Returns the current question_uuid or a new one if no current exists
     *
     * @return DomainObjectId
     */
    private function currentOrNewQuestionId() : DomainObjectId
    {
        if (isset($_GET[\AsqQuestionAuthoringGUI::VAR_QUESTION_ID])) {
            return new DomainObjectId($_GET[\AsqQuestionAuthoringGUI::VAR_QUESTION_ID]);
        }
        
        return new DomainObjectId();
    }

    /**
     * @throws ilCtrlException
     */
	public function executeCommand()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */

        $DIC->ctrl()->setParameter(
            $this, self::VAR_QUESTION_ID, $this->question_id->getId()
        );

		switch( $DIC->ctrl()->getNextClass() )
        {
            case strtolower(AsqQuestionCreationGUI::class):

                $gui = new AsqQuestionCreationGUI($this->authoring_context_container);

                $DIC->ctrl()->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionPreviewGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $DIC->tabs()->activateTab(self::TAB_ID_PREVIEW);

                $gui = new AsqQuestionPreviewGUI($this->question_id);

                $DIC->ctrl()->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionPageGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $DIC->tabs()->activateTab(self::TAB_ID_PAGEVIEW);

                $gui = AsqGateway::get()->ui()->getQuestionPage(
                    AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id->getId()));

                if (strlen($DIC->ctrl()->getCmd()) == 0 && !isset($_POST["editImagemapForward_x"]))
                {
                    // workaround for page edit imagemaps, keep in mind

                    $DIC->ctrl()->setCmdClass(strtolower(get_class($gui)));
                    $DIC->ctrl()->setCmd('preview');
                }

                $html = $DIC->ctrl()->forwardCommand($gui);
                $DIC->ui()->mainTemplate()->setContent($html);

                break;

            case strtolower(AsqQuestionConfigEditorGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $DIC->tabs()->activateTab(self::TAB_ID_CONFIG);

                $gui = new AsqQuestionConfigEditorGUI(
                    $this->authoring_context_container,
                    $this->question_id);
                $DIC->ctrl()->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionFeedbackEditorGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $DIC->tabs()->activateTab(self::TAB_ID_FEEDBACK);

                $gui = new AsqQuestionFeedbackEditorGUI(
                    AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id->getId())
                );
                $DIC->ctrl()->forwardCommand($gui);

                break;

            case strtolower(AsqQuestionHintEditorGUI::class):

                $this->initHeaderAction();
                $this->initAuthoringTabs();
                $DIC->tabs()->activateTab(self::TAB_ID_HINTS);

                $gui = new AsqQuestionHintEditorGUI(AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id->getId()));
                $DIC->ctrl()->forwardCommand($gui);

                break;

            case strtolower(ilCommonActionDispatcherGUI::class):

                $gui = ilCommonActionDispatcherGUI::getInstanceFromAjaxCall();
                $DIC->ctrl()->forwardCommand($gui);

                break;

            case strtolower(self::class):
            default:

                $cmd = $DIC->ctrl()->getCmd();
                $this->{$cmd}();
        }
	}


    protected function redrawHeaderAction()
    {
        global $DIC; /* @var ILIAS\DI\Container $DIC */
        echo $this->getHeaderAction() . $DIC->ui()->mainTemplate()->getOnLoadCodeForAsynch();
        exit;
    }


    protected function initHeaderAction()
    {
        global $DIC; /* @var ILIAS\DI\Container $DIC */

        $DIC->ui()->mainTemplate()->setVariable(
            'HEAD_ACTION', $this->getHeaderAction()
        );

        $notesUrl = $DIC->ctrl()->getLinkTargetByClass(
            array('ilCommonActionDispatcherGUI', 'ilNoteGUI'), '', '', true, false
        );

        ilNoteGUI::initJavascript($notesUrl,IL_NOTE_PUBLIC, $DIC->ui()->mainTemplate());

        $redrawActionsUrl = $DIC->ctrl()->getLinkTarget(
            $this, self::CMD_REDRAW_HEADER_ACTION_ASYNC, '', true
        );

        $DIC->ui()->mainTemplate()->addOnLoadCode("il.Object.setRedrawAHUrl('$redrawActionsUrl');");
    }


    protected function getHeaderAction() : string
    {
        global $DIC; /* @var ILIAS\DI\Container $DIC */

        //$question = $this->authoring_application_service->GetQuestion($this->question_id->getId());

        /**
         * TODO: Get the old integer id of the question.
         * We still need the former integer sequence id of the question,
         * since several other services in ilias does only work with an int id.
         */

        //$integerQuestionId = $question->getLegacyIntegerId(); // or similar
        $integerQuestionId = 0;

        $dispatcher = new ilCommonActionDispatcherGUI(
            ilCommonActionDispatcherGUI::TYPE_REPOSITORY, $DIC->access(),
            $this->authoring_context_container->getObjType(),
            $this->authoring_context_container->getRefId(),
            $this->authoring_context_container->getObjId()
        );

        $dispatcher->setSubObject('quest', $integerQuestionId);

        $ha = $dispatcher->initHeaderAction();
        $ha->enableComments(true, false);

        return $ha->getHeaderAction($DIC->ui()->mainTemplate());
    }


    protected function initAuthoringTabs()
    {
        global $DIC; /* @var ILIAS\DI\Container $DIC */

        $question_dto = AsqGateway::get()->question()->getQuestionByQuestionId($this->question_id->getId());
        
        $DIC->tabs()->clearTargets();

        $DIC->tabs()->setBackTarget(
            $this->authoring_context_container->getBackLink()->getLabel(),
            $this->authoring_context_container->getBackLink()->getAction()
        );

        if(is_object($question_dto->getData()) > 0)
        {
            $link = AsqGateway::get()->link()->getEditPageLink($this->question_id->getId());
            $DIC->tabs()->addTab(self::TAB_ID_PAGEVIEW, $link->getLabel(), $link->getAction());
        }
        if(is_object($question_dto->getData()) > 0) {
            $link = AsqGateway::get()->link()->getPreviewLink($this->question_id->getId());
            $DIC->tabs()->addTab(self::TAB_ID_PREVIEW, $link->getLabel(), $link->getAction());
        }

        $link = AsqGateway::get()->link()->getEditLink($this->question_id->getId());
        $DIC->tabs()->addTab(self::TAB_ID_CONFIG, $link->getLabel(), $link->getAction());

        if(is_object($question_dto->getData()) > 0) {
            $link = AsqGateway::get()->link()->getEditFeedbacksLink($this->question_id->getId());
            $DIC->tabs()->addTab(self::TAB_ID_FEEDBACK, $link->getLabel(), $link->getAction());
        }
        if(is_object($question_dto->getData()) > 0) {
            $link = AsqGateway::get()->link()->getEditHintsLink($this->question_id->getId());
            $DIC->tabs()->addTab(self::TAB_ID_HINTS, $link->getLabel(), $link->getAction());
        }
    }
}