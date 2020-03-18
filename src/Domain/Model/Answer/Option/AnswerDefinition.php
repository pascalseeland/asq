<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Answer\Option;

use JsonSerializable;
use stdClass;
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
abstract class AnswerDefinition implements JsonSerializable {

    /**
     * @return AsqTableInputFieldDefinition[]
     */
    public abstract static function getFields(QuestionPlayConfiguration $play) : array;

	public abstract function getValues() : array;

	public abstract static function getValueFromPost(string $index);

	public abstract static function deserialize(stdClass $data);
	
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
	
	protected static function getPostKey(int $index, string $name) {
	    return sprintf('%s_answer_options_%s', $index, $name);
	}
	
	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize() {
	    return get_object_vars($this);
	}
}