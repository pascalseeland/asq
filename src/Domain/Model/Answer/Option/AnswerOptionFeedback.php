<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Answer\Option;

use JsonSerializable;
use stdClass;
use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class AnswerOptionFeedback
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AnswerOptionFeedback implements JsonSerializable
{

    const VAR_FEEDBACK_FOR_ANSWER = "feedback_for_answer";
    /**
     * @var string
     */
    protected $answer_feedback;


    public function __construct(? string $answer_feedback = "")
    {
        $this->answer_feedback = $answer_feedback;
    }


    /**
     * @return string
     */
    public function getAnswerFeedback() : ?string
    {
        return $this->answer_feedback;
    }


    public static function deserialize(stdClass $data)
    {
        return new AnswerOptionFeedback($data->answer_feedback);
    }


    public function getValues() : array
    {
        return [self::VAR_FEEDBACK_FOR_ANSWER => $this->answer_feedback];
    }


    function equals(AbstractValueObject $other) : bool
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }

        if ($this->getAnswerFeedback() !== $other->getAnswerFeedback()) {
            return false;
        }

        return true;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}