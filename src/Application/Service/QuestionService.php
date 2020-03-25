<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Command\CommandBusBuilder;
use srag\asq\Application\Command\CreateQuestionCommand;
use srag\asq\Application\Command\SaveQuestionCommand;
use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\QuestionRepository;
use srag\asq\Domain\Model\ContentEditingMode;
use srag\asq\Domain\Model\Question;
use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStore;
use srag\asq\Application\Command\CreateQuestionRevisionCommand;

/**
 * Class QuestionService
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionService extends ASQService
{
    /**
     * @param string $id
     * @throws AsqException
     * @return QuestionDto
     */
    public function getQuestionByQuestionId(string $id) : QuestionDto {
        $question = QuestionRepository::getInstance()->getAggregateRootById(new DomainObjectId($id));
        
        if(is_object($question->getAggregateId())) {
            return QuestionDto::CreateFromQuestion($question);
        }
        else {
            //TODO translate?
            throw new AsqException(sprintf("Question with id %s does not exist", $id));
        }
    }

    /**
     * @param string $name
     * @param string $question_id
     */
    public function createQuestionRevision(string $name, string $question_id) {
        CommandBusBuilder::getCommandBus()->handle(new CreateQuestionRevisionCommand($question_id, $name, $this->getActiveUser()));
    }
    
    /**
     * @param int $container_id
     * @return QuestionDto[]
     */
    public function getQuestionsOfContainer(int $container_id) : array {
        $questions = [];
        $event_store = new QuestionEventStore();
        foreach ($event_store->allStoredQuestionIdsForContainerObjId($container_id) as $aggregate_id) {        
            $questions[] = $this->getQuestionByQuestionId($aggregate_id);;
        }
        
        return $questions;
    }

    /**
     * @param int $type
     * @param int $container_id
     * @param string $content_editing_mode
     * @return QuestionDto
     */
    public function createQuestion(int $type, int $container_id, string $content_editing_mode = ContentEditingMode::RTE_TEXTAREA): QuestionDto
    {
        $id = new DomainObjectId();

        CommandBusBuilder::getCommandBus()->handle(
            new CreateQuestionCommand(
                $id,
                $type, 
                $this->getActiveUser(), 
                $container_id, 
                $content_editing_mode));
        
        return $this->getQuestionByQuestionId($id->getId());
    }

    /**
     * @param QuestionDto $question_dto
     */
    public function saveQuestion(QuestionDto $question_dto)
    {
        // check changes and trigger them on question if there are any
        /** @var Question $question */
        $question = QuestionRepository::getInstance()->getAggregateRootById(new DomainObjectId($question_dto->getId()));
        
        if (! AbstractValueObject::isNullableEqual($question_dto->getData(), $question->getData())) {
            $question->setData($question_dto->getData(), $this->getActiveUser());
        }

        if (! AbstractValueObject::isNullableEqual($question_dto->getPlayConfiguration(), $question->getPlayConfiguration())) {
            $question->setPlayConfiguration($question_dto->getPlayConfiguration(), $this->getActiveUser());
        }

        if (! $question_dto->getAnswerOptions()->equals($question->getAnswerOptions())) {
            $question->setAnswerOptions($question_dto->getAnswerOptions(), $this->getActiveUser());
        }

        if (is_object($question_dto->getFeedback()) && !AbstractValueObject::isNullableEqual($question_dto->getFeedback(), $question->getFeedback())) {
            $question->setFeedback($question_dto->getFeedback(), $this->getActiveUser());
        }

        if (! is_null($question_dto->getQuestionHints()) && !$question_dto->getQuestionHints()->equals($question->getHints())) {
            $question->setHints($question_dto->getQuestionHints(), $this->getActiveUser());
        }

        if (count($question->getRecordedEvents()->getEvents()) > 0) {
            // save changes if there are any
            CommandBusBuilder::getCommandBus()->handle(new SaveQuestionCommand($question, $this->getActiveUser()));
        }
    }
}