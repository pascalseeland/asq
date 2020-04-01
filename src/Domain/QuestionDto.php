<?php
declare(strict_types = 1);
namespace srag\asq\Domain;

use srag\asq\Domain\Model\ContentEditingMode;
use srag\asq\Domain\Model\Feedback;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\QuestionData;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Hint\QuestionHints;

/**
 * Class QuestionDto
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *           
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionDto
{

    const IL_COMPONENT_ID = 'asq';

    /**
     *
     * @var string
     */
    private $id;

    /**
     *
     * @var int
     */
    private $type;

    /**
     *
     * @var string
     */
    private $revision_name = "";

    /**
     * var string
     */
    private $il_component_id = self::IL_COMPONENT_ID;

    /**
     *
     * @var QuestionData
     */
    private $data;

    /**
     *
     * @var QuestionPlayConfiguration
     */
    private $play_configuration;

    /**
     *
     * @var AnswerOptions
     */
    private $answer_options;

    /**
     *
     * @var Feedback
     */
    private $feedback;

    /**
     *
     * @var QuestionHints
     */
    private $question_hints;

    /**
     *
     * @var bool
     */
    private $complete = false;

    /**
     *
     * @param Question $question
     *
     * @return QuestionDto
     */
    public static function CreateFromQuestion(Question $question): QuestionDto
    {
        $dto = new QuestionDto();

        $dto->id = $question->getAggregateId()->getId();
        $dto->type = $question->getType();
        $dto->complete = $question->isQuestionComplete();

        if ($question->getRevisionId() !== null) {
            $dto->revision_name = $question->getRevisionId()->getName();
        }

        $dto->data = $question->getData();
        $dto->play_configuration = $question->getPlayConfiguration();
        $dto->answer_options = $question->getAnswerOptions();

        $dto->feedback = $question->getFeedback() ?? new Feedback();
        $dto->question_hints = $question->getHints();

        return $dto;
    }

    public function __construct()
    {
        $this->answer_options = new AnswerOptions();
    }

    /**
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
    
    /**
     *
     * @param bool $complete
     */
    public function setComplete(bool $complete)
    {
        $this->complete = $complete;
    }

    /**
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->complete;
    }

    /**
     *
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getRevisionName(): string
    {
        return $this->revision_name;
    }

    /**
     *
     * @return QuestionData
     */
    public function getData(): ?QuestionData
    {
        return $this->data;
    }

    /**
     *
     * @param QuestionData $data
     */
    public function setData(QuestionData $data): void
    {
        $this->data = $data;
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
     *
     * @param QuestionPlayConfiguration $play_configuration
     */
    public function setPlayConfiguration(QuestionPlayConfiguration $play_configuration): void
    {
        $this->play_configuration = $play_configuration;
    }

    /**
     *
     * @return AnswerOptions
     */
    public function getAnswerOptions(): AnswerOptions
    {
        return $this->answer_options;
    }

    /**
     *
     * @param AnswerOptions $answer_options
     */
    public function setAnswerOptions(AnswerOptions $answer_options): void
    {
        $this->answer_options = $answer_options;
    }

    /**
     *
     * @param Feedback $feedback
     */
    public function setFeedback(?Feedback $feedback): void
    {
        $this->feedback = $feedback;
    }

    /**
     *
     * @return Feedback
     */
    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
    }

    /**
     * @return bool
     */
    public function hasHints() : bool
    {
        return !is_null($this->question_hints) && count($this->question_hints->getHints()) > 0;
    }
    
    /**
     *
     * @return QuestionHints
     */
    public function getQuestionHints(): ?QuestionHints
    {
        return $this->question_hints;
    }

    /**
     *
     * @param QuestionHints $question_hints
     */
    public function setQuestionHints(QuestionHints $question_hints): void
    {
        $this->question_hints = $question_hints;
    }
}