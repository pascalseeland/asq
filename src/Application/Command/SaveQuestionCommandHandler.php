<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use srag\CQRS\Command\CommandContract;
use srag\CQRS\Command\CommandHandlerContract;
use srag\asq\Domain\QuestionRepository;

/**
 * Class SaveQuestionCommandHandler
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class SaveQuestionCommandHandler implements CommandHandlerContract {

	/**
	 * @param CommandContract $command
	 */
	public function handle(CommandContract $command) {
	    /** @var SaveQuestionCommand $command */
		QuestionRepository::getInstance()->save($command->GetQuestion());
	}
}