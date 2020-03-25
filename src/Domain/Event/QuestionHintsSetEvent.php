<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractDomainEvent;
use srag\asq\Domain\Model\Hint\QuestionHints;
use ilDateTime;

/**
 * Class QuestionHintsSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHintsSetEvent extends AbstractDomainEvent {
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
        ilDateTime $occured_on,
        int $initiating_user_id, 
        QuestionHints $hints = null)
    {
        parent::__construct($aggregate_id, $occured_on, $initiating_user_id);
        
        $this->hints = $hints;
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