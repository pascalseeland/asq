<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractIlContainerItemDomainEvent;
use srag\asq\Domain\Model\Feedback;

/**
 * Class QuestionFeedbackSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionFeedbackSetEvent extends AbstractIlContainerItemDomainEvent {

	public const NAME = 'QuestionFeedbackSetEvent';
	/**
	 * @var Feedback
	 */
	protected $feedback;


    /**
     * @param DomainObjectId $aggregate_id
     * @param int $container_obj_id
     * @param int $initiating_user_id
     * @param int $question_int_id
     * @param Feedback $feedback
     */
	public function __construct(DomainObjectId $aggregate_id, 
	                            int $container_obj_id, 
	                            int $initiating_user_id, 
	                            int $question_int_id,
                                Feedback $feedback = null)
	{
	    parent::__construct($aggregate_id, $question_int_id, $container_obj_id, $initiating_user_id);
	    
		$this->feedback = $feedback;
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
	 * @return Feedback
	 */
	public function getFeedback(): Feedback {
		return $this->feedback;
	}

    /**
     * @return string
     */
	public function getEventBody(): string {
		return json_encode($this->feedback);
	}

	/**
	 * @param string $json_data
	 */
	public function restoreEventBody(string $json_data) : void {
		$this->feedback = Feedback::deserialize($json_data);
	}
}