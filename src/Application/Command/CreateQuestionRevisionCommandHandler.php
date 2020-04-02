<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use srag\CQRS\Aggregate\DomainObjectId;
use srag\CQRS\Aggregate\RevisionFactory;
use srag\CQRS\Command\CommandContract;
use srag\CQRS\Command\CommandHandlerContract;
use srag\asq\Domain\QuestionRepository;
use srag\asq\Infrastructure\Persistence\Projection\PublishedQuestionRepository;
use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;

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
	    $repository = new PublishedQuestionRepository();
	    
	    if ($repository->revisionExists($command->getQuestionId(), $command->getRevisionName())) {
	       throw new AsqException(
	           sprintf(
	               'A revision with the Name: "%s" already exists for Question: "%s"', 
	               $command->getRevisionName(), 
	               $command->getQuestionId()));
	    }
	    
	    
		$question = QuestionRepository::getInstance()->getAggregateRootById(new DomainObjectId($command->getQuestionId()));
		RevisionFactory::setRevisionId($question, $command->getRevisionName());

		$repository->saveNewQuestionRevision(QuestionDto::CreateFromQuestion($question));
	    
		QuestionRepository::getInstance()->save($question);
	}
}