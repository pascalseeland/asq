<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractIlContainerItemDomainEvent;
use srag\asq\Domain\Model\Hint\QuestionHints;

/**
 * Class QuestionHintsSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHintsSetEvent extends AbstractIlContainerItemDomainEvent {

    public const NAME = 'QuestionHintsSetEvent';
    /**
     * @var QuestionHints
     */
    protected $hints;


    /**
     * QuestionHintsSetEvent constructor.
     *
     * @param DomainObjectId     $id
     * @param int                $container_obj_id
     * @param int                $initiating_user_id
     * @param QuestionHints|null $hints
     *
     * @throws \ilDateTimeException
     */
    public function __construct(DomainObjectId $aggregate_id,
        int $container_obj_id,
        int $initiating_user_id,
        int $question_int_id,
        QuestionHints $hints = null)
    {
        parent::__construct($aggregate_id, $question_int_id, $container_obj_id, $initiating_user_id);
        
        $this->hints = $hints;
    }

    /**
     * @return string
     *
     * Add a Constant EVENT_NAME to your class: Name it: Classname
     * e.g. 'QuestionCreatedEvent'
     */
    public function getEventName(): string {
        return self::NAME;
    }

    /**
     * @return QuestionHints
     */
    public function getHints(): QuestionHints {
        return $this->hints;
    }

    public function getEventBody(): string {
        return json_encode($this->hints->getHints());
    }

    /**
     * @param string $json_data
     */
    public function restoreEventBody(string $json_data) : void {
        $this->hints = QuestionHints::deserialize($json_data);
    }
}