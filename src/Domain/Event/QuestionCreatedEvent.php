<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractIlContainerItemDomainEvent;

/**
 * Class QuestionCreatedEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionCreatedEvent extends AbstractIlContainerItemDomainEvent {

	public const NAME = 'QuestionCreatedEvent';

	/**
	 * @param string $question_uuid
	 * @param int $container_obj_id
	 * @param int $initiating_user_id
	 * @param int $object_id
	 */
	public function __construct(DomainObjectId $aggregate_id,
	                            int $container_obj_id,
	                            int $initiating_user_id,
	                            int $question_int_id) 
	{
	    parent::__construct($aggregate_id, $question_int_id, $container_obj_id, $initiating_user_id);
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

    public function restoreEventBody(string $json_data) : void
    {
        //no additional fields
    }
    
    public function getEventBody(): string
    {
        //no additional fields
        return '';
    }
}