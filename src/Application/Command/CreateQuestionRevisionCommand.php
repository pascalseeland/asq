<?php
declare(strict_types=1);

namespace srag\asq\Application\Command;

use srag\CQRS\Command\AbstractCommand;
use srag\CQRS\Command\CommandContract;

/**
 * Class CreateQuestionRevisionCommand
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class CreateQuestionRevisionCommand extends AbstractCommand {

	/**
	 * @var string
	 */
	private $question_id;


	public function __construct(string $question_id, int $issuer_id) {
		parent::__construct($issuer_id);
		$this->question_id = $question_id;
	}


	/**
	 * @return string
	 */
	public function getQuestionId(): string {
		return $this->question_id;
	}
}
