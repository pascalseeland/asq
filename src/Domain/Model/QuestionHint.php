<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use srag\CQRS\Aggregate\AbstractValueObject;

/**
 * Class QuestionHint
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHint extends AbstractValueObject{

	/**
	 * @var string
	 */
	protected $label_hint;
	/**
	 * @var float
	 */
	protected $points;


	/**
	 * @param string $label_hint
	 * @param float  $points
	 *
	 * @return QuestionHint
	 */
	static function create(string $label_hint, float $points) : QuestionHint{
		$object = new QuestionHint();
		$object->label_hint = $label_hint;
		$object->points = $points;
		return $object;
	}


	/**
	 * @return string
	 */
	public function getLabelHint(): string {
		return $this->label_hint;
	}


	/**
	 * @return float
	 */
	public function getPoints(): float {
		return $this->points;
	}

    /**
     * @param AbstractValueObject $other
     *
     * @return bool
     */
    public function equals(AbstractValueObject $other): bool
    {
        /** @var QuestionHint $other */
        return get_class($this) === get_class($other) &&
               $this->getLabelHint() === $other->getLabelHint() &&
               $this->getPoints() === $other->points;
    }
}