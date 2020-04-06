<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Persistence\EventStore;

use ILIAS\UI\NotImplementedException;
use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractDomainEvent;
use srag\CQRS\Event\DomainEvents;
use srag\CQRS\Event\EventID;
use srag\CQRS\Event\EventStore;
use srag\asq\Application\Exception\AsqException;
use ilDateTime;

/**
 * Class QuestionEventStore
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionEventStore extends EventStore {

	/**
	 * @param DomainEvents $events
	 *
	 * @return void
	 */
	public function commit(DomainEvents $events) : void {
		/** @var AbstractDomainEvent $event */
		foreach ($events->getEvents() as $event) {
			$stored_event = new QuestionEventStoreAr();
			
			$stored_event->setEventData(
			    new EventID(),
			    $event->getAggregateId(),
			    $event->getEventName(),
			    $event->getOccurredOn(),
			    $event->getInitiatingUserId(),
			    $event->getEventBody(),
			    get_class($event));

			$stored_event->create();
		}
	}


	/**
	 * @param DomainObjectId $id
	 *
	 * @return DomainEvents
	 */
	public function getAggregateHistoryFor(DomainObjectId $id): DomainEvents {
	    global $DIC;
	    
	    $sql = "SELECT * FROM " . QuestionEventStoreAr::STORAGE_NAME . " where aggregate_id = " . $DIC->database()->quote($id->getId(),'string');
	    $res = $DIC->database()->query($sql);
	    
	    if ($res->rowCount() === 0) {
	        throw new AsqException('Aggregate does not exist');
	    }
	    
	    $event_stream = new DomainEvents();
	    while ($row = $DIC->database()->fetchAssoc($res)) {
	        /**@var AbstractDomainEvent $event */
	        $event_name = $row['event_class'];
	        $event = $event_name::restore(
	            new EventID($row['event_id']),
	            new DomainObjectId($row['aggregate_id']),
	            intval($row['initiating_user_id']),
	            new ilDateTime($row['occurred_on']),
	            $row['event_body']);
	        $event_stream->addEvent($event);
	    }
	    
	    return $event_stream;
	}
	
    public function getEventStream(?EventID $from_position): DomainEvents
    {
        throw new NotImplementedException("Implement evenstream when needed");
    }
}