<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Answer\Option;

use stdClass;
use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

/**
 * Abstract Class AnswerDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AnswerDefinition Extends AbstractValueObject {

    /**
     * @return AsqTableInputFieldDefinition[]
     */
    public abstract static function getFields(QuestionPlayConfiguration $play) : array;

	public abstract function getValues() : array;

	public abstract static function getValueFromPost(string $index);
	
	/**
	 * @return bool
	 */
	public static function checkInput(int $count) : bool {
	    return true;
	}
	
	/**
	 * @return string
	 */
	public static function getErrorMessage() : string {
	    return '';
	}
	
	protected static function getPostKey(string $index, string $name) {
	    return sprintf('%s_answer_options_%s', $index, $name);
	}
}