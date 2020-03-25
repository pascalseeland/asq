<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use ilDateTime;
use srag\CQRS\Aggregate\AbstractEventSourcedAggregateRoot;
use srag\CQRS\Aggregate\AggregateRoot;
use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Aggregate\IsRevisable;
use srag\CQRS\Aggregate\RevisionId;
use srag\CQRS\Event\DomainEvent;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\Standard\AggregateCreatedEvent;
use srag\asq\Domain\Event\AggregateRevisionCreatedEvent;
use srag\asq\Domain\Event\QuestionAnswerOptionsSetEvent;
use srag\asq\Domain\Event\QuestionDataSetEvent;
use srag\asq\Domain\Event\QuestionFeedbackSetEvent;
use srag\asq\Domain\Event\QuestionHintsSetEvent;
use srag\asq\Domain\Event\QuestionPlayConfigurationSetEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Hint\QuestionHints;

/**
 * Class Question
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class Question extends AbstractEventSourcedAggregateRoot implements IsRevisable
{
    const VAR_TYPE = 'question_type';
    
    /**
     * @var int
     */
    private $question_type;
    /**
     * @var RevisionId
     */
    private $revision_id;
    /**
     * @var string
     */
    private $revision_name;
    /**
     * @var int
     */
    private $creator_id;
    /**
     * @var QuestionData
     */
    private $data;
    /**
     * @var QuestionPlayConfiguration
     */
    private $play_configuration;
    /**
     * @var AnswerOptions
     */
    private $answer_options;
    /**
     * @var QuestionHints
     */
    private $hints;
    /**
     * @var Feedback
     */
    private $feedback;


    /**
     * Question constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->answers = [];
        $this->answer_options = new AnswerOptions();
        $this->hints = new QuestionHints([]);
    }


    /**
     * @param DomainObjectId $question_uuid
     * @param int            $initiating_user_id
     *
     * @return Question
     */
    public static function createNewQuestion(
        DomainObjectId $question_uuid,
        int $initiating_user_id,
        int $question_type
    ) : Question {
        $question = new Question();
        $question->ExecuteEvent(
            new AggregateCreatedEvent(
                $question_uuid,
                new ilDateTime(time(), IL_CAL_UNIX),
                $initiating_user_id,
                [self::VAR_TYPE => $question_type]
            ));

        return $question;
    }


    /**
     * @param AggregateCreatedEvent $event
     */
    /**
     * @param AggregateCreatedEvent $event
     */
    protected function applyAggregateCreatedEvent(DomainEvent $event) {
        parent::applyAggregateCreatedEvent($event);
        $this->creator_id = $event->getInitiatingUserId();
        $this->question_type = $event->getAdditionalData()[self::VAR_TYPE];
    }


    /**
     * @param QuestionDataSetEvent $event
     */
    protected function applyQuestionDataSetEvent(QuestionDataSetEvent $event)
    {
        $this->data = $event->getData();
    }


    /**
     * @param QuestionPlayConfigurationSetEvent $event
     */
    protected function applyQuestionPlayConfigurationSetEvent(QuestionPlayConfigurationSetEvent $event)
    {
        $this->play_configuration = $event->getPlayConfiguration();
    }


    /**
     * @param AggregateRevisionCreatedEvent $event
     */
    protected function applyAggregateRevisionCreatedEvent(AggregateRevisionCreatedEvent $event)
    {
        $this->revision_id = new RevisionId($event->getRevisionId());
    }


    /**
     * @param QuestionAnswerOptionsSetEvent $event
     */
    protected function applyQuestionAnswerOptionsSetEvent(QuestionAnswerOptionsSetEvent $event)
    {
        $this->answer_options = $event->getAnswerOptions();
    }


    /**
     * @param QuestionHintsSetEvent $event
     */
    protected function applyQuestionHintsSetEvent(QuestionHintsSetEvent $event)
    {
        $this->hints = $event->getHints();
    }

    /**
     * @param QuestionFeedbackSetEvent $event
     */
    protected function applyQuestionFeedbackSetEvent(QuestionFeedbackSetEvent $event)
    {
        $feedback = $event->getFeedback();
        $this->feedback = $feedback;
    }
    
    /**
     * @return int
     */
    public function getType() : int {
        return $this->question_type;
    }

    /**
     * @return QuestionData
     */
    public function getData() : ?QuestionData
    {
        return $this->data;
    }


    /**
     * @param QuestionData $data
     * @param int          $container_obj_id
     * @param int          $creator_id
     */
    public function setData(QuestionData $data, int $creator_id)
    {
        $this->ExecuteEvent(new QuestionDataSetEvent($this->getAggregateId(), new ilDateTime(time(), IL_CAL_UNIX), $creator_id, $data));
    }

    /**
     *
     * @return QuestionPlayConfiguration
     */
    public function getPlayConfiguration(): ?QuestionPlayConfiguration
    {
        return $this->play_configuration;
    }


    /**
     * @param QuestionPlayConfiguration $play_configuration
     * @param int                       $creator_id
     */
    public function setPlayConfiguration(
        QuestionPlayConfiguration $play_configuration,
        int $creator_id
    ) : void {
        $this->ExecuteEvent(new QuestionPlayConfigurationSetEvent(
            $this->getAggregateId(),
            new ilDateTime(time(), IL_CAL_UNIX),
            $creator_id,
            $play_configuration));
    }

    /**
     * @return AnswerOptions
     */
    public function getAnswerOptions() : AnswerOptions
    {
        return $this->answer_options;
    }

    /**
     *
     * @param AnswerOptions $options
     * @param int $creator_id
     */
    public function setAnswerOptions(AnswerOptions $options, int $creator_id)
    {
        $this->ExecuteEvent(new QuestionAnswerOptionsSetEvent($this->getAggregateId(), new ilDateTime(time(), IL_CAL_UNIX), $creator_id, $options));
    }


    /**
     * @return QuestionHints
     */
    public function getHints() : ?QuestionHints
    {
        return $this->hints;
    }


    /**
     * @param QuestionHints $hints
     * @param int           $creator_id
     */
    public function setHints(QuestionHints $hints, int $creator_id = self::SYSTEM_USER_ID)
    {
        $this->ExecuteEvent(new QuestionHintsSetEvent(
            $this->getAggregateId(),
            new ilDateTime(time(), IL_CAL_UNIX),
            $creator_id,
            $hints));
    }

    /**
     * @return Feedback
     */
    public function getFeedback() : ?Feedback
    {
        return $this->feedback;
    }


    /**
     * @param Feedback $feedback
     * @param int $creator_id
     */
    public function setFeedback(
        Feedback $feedback,
        int $creator_id
    ) : void {
        $this->ExecuteEvent(new QuestionFeedbackSetEvent(
            $this->getAggregateId(),
            new ilDateTime(time(), IL_CAL_UNIX),
            $creator_id,
            $feedback));
    }

    /**
     * @return int
     */
    public function getCreatorId() : int
    {
        return $this->creator_id;
    }

    /**
     * @param int $creator_id
     */
    public function setCreatorId(int $creator_id) : void
    {
        $this->creator_id = $creator_id;
    }


    /**
     * @return RevisionId revision id of object
     */
    public function getRevisionId() : ?RevisionId
    {
        return $this->revision_id;
    }

    /**
     * @param RevisionId $id
     *
     * @return mixed|void
     */
    public function setRevisionId(RevisionId $id, int $user_id)
    {
        $this->ExecuteEvent(new AggregateRevisionCreatedEvent(
            $this->getAggregateId(),
            new ilDateTime(time(), IL_CAL_UNIX),
            $user_id,
            $id));
    }

    /**
     * @return array
     *
     * Data used for signing the revision, so this method needs to to collect all
     * Domain specific data of an object and return it as an array
     */
    public function getRevisionData() : array
    {
        $data = [];
        $data[] = $this->getAggregateId()->getId();
        $data[] = $this->getData();
        $data[] = $this->getPlayConfiguration();
        $data[] = $this->getAnswerOptions();
        $data[] = $this->getFeedback();
        $data[] = $this->getHints();
        return $data;
    }
    
    public function isQuestionComplete() : bool {
        //TODO as soon as presenter gets meat, check for presence of presenter
        if (is_null($this->data) ||
            is_null($this->play_configuration) ||
            is_null($this->play_configuration->getEditorConfiguration()) ||
            is_null($this->play_configuration->getScoringConfiguration())) 
        {
            return false;        
        }

        return $this->data->isComplete() &&
               QuestionPlayConfiguration::isComplete($this);
    }
}