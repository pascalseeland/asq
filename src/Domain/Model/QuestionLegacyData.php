<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class QuestionLegacyData
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionLegacyData extends AbstractValueObject {	
	/**
	 * @var ?int
	 */
	protected $answer_type_id;
	
	/**
	 * @var ?string
	 */
	protected $content_editing_mode;

	
	/**
	 * @param int      $answer_type_id
	 *
	 * @return QuestionLegacyData
	 */
	static function create(?int $answer_type_id, ?string $content_editing_mode) : QuestionLegacyData {
		$object = new QuestionLegacyData();
		$object->answer_type_id = $answer_type_id;
		$object->content_editing_mode = $content_editing_mode;
		return $object;
	}

	/**
	 * @return int
	 */
	public function getAnswerTypeId(): ?int {
		return $this->answer_type_id;
	}
	
	public function getContentEditingMode(): ?string {
	    return $this->content_editing_mode;
	}
}