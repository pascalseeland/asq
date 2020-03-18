<?php
declare(strict_types=1);

namespace srag\asq\Domain\Event;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Event\AbstractIlContainerItemDomainEvent;
use srag\asq\Domain\Model\QuestionPlayConfiguration;

/**
 * Class QuestionPlayConfigurationSetEvent
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionPlayConfigurationSetEvent extends AbstractIlContainerItemDomainEvent {

	public const NAME = 'QuestionPlayConfigurationSetEvent';
	/**
	 * @var QuestionPlayConfiguration
	 */
	protected $play_configuration;


    /**
     * QuestionPlayConfigurationSetEvent constructor.
     *
     * @param DomainObjectId                 $id
     * @param int                            $container_obj_id
     * @param int                            $initiating_user_id
     * @param QuestionPlayConfiguration|null $play_configuration
     *
     * @throws \ilDateTimeException
     */
	public function __construct(DomainObjectId $aggregate_id, 
	                            int $container_obj_id, 
	                            int $initiating_user_id, 
	                            int $question_int_id, 
	                            QuestionPlayConfiguration $play_configuration = null)
	{
	    parent::__construct($aggregate_id, $question_int_id, $container_obj_id, $initiating_user_id);
	    
		$this->play_configuration = $play_configuration;
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
	 * @return QuestionPlayConfiguration
	 */
	public function getPlayConfiguration(): QuestionPlayConfiguration {
		return $this->play_configuration;
	}

    /**
     * @return string
     */
	public function getEventBody(): string {
		return json_encode($this->play_configuration);
	}

	/**
	 * @param string $json_data
	 */
	public function restoreEventBody(string $json_data) : void {
		$this->play_configuration = AbstractValueObject::deserialize($json_data);
	}
}