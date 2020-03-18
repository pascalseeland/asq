<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Aggregate\RevisionFactory;
use srag\CQRS\Command\CommandContract;
use srag\CQRS\Command\CommandHandlerContract;
use srag\asq\Domain\QuestionRepository;
use srag\asq\Domain\Projection\ProjectQuestions;

/**
 * Class CreateQuestionRevisionCommandHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class CreateQuestionRevisionCommandHandler implements CommandHandlerContract {

    /**
     * @param CommandContract $command
     */
	public function handle(CommandContract $command) {
	    /** @var CreateQuestionRevisionCommand $command */
		$question = QuestionRepository::getInstance()->getAggregateRootById(new DomainObjectId($command->getQuestionId()));
		RevisionFactory::setRevisionId($question);
		$projector = new ProjectQuestions();
		$projector->project($question);
		QuestionRepository::getInstance()->save($question);
	}
}