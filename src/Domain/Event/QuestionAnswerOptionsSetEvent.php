<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractIlContainerItemDomainEvent;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;

/**
 * Class QuestionAnswerOptionsSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionAnswerOptionsSetEvent extends AbstractIlContainerItemDomainEvent {

	public const NAME = 'QuestionAnswerOptionsSetEvent';
	/**
	 * @var AnswerOptions
	 */
	protected $answer_options;


    /**
     * QuestionAnswerOptionsSetEvent constructor.
     *
     * @param DomainObjectId     $id
     * @param int                $container_obj_id
     * @param int                $initiating_user_id
     * @param AnswerOptions|null $options
     *
     * @throws \ilDateTimeException
     */
	public function __construct(DomainObjectId $aggregate_id, 
	                            int $container_obj_id, 
	                            int $initiating_user_id, 
	                            int $question_int_id, 
	                            AnswerOptions $options = null)
	{
	    parent::__construct($aggregate_id, $question_int_id, $container_obj_id, $initiating_user_id);
	    
		$this->answer_options = $options;
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
	 * @return AnswerOptions
	 */
	public function getAnswerOptions(): AnswerOptions {
		return $this->answer_options;
	}

	public function getEventBody(): string {
		return json_encode($this->answer_options->getOptions());
	}

	/**
	 * @param string $json_data
	 */
	public function restoreEventBody(string $json_data) : void {
        $this->answer_options = AnswerOptions::deserialize($json_data);
	}
}