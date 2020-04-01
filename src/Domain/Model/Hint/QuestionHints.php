<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Hint;

use srag\asq\Application\Exception\AsqException;

/**
 * Class Hints
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class QuestionHints
{


    /**
     * @var QuestionHint[]
     */
    private $hints;


    /**
     * QuestionHints constructor.
     *
     * @param array Hint[]
     *
     * @throws AsqException
     */
    public function __construct(array $hints = [])
    {
        $this->hints = $hints;
    }


    public function addHint(?QuestionHint $hint)
    {
        $this->hints[] = $hint;
    }


    /**
     * @return QuestionHint[]
     */
    public function getHints() : array
    {
        return $this->hints;
    }


    /**
     * @param int $order_number
     *
     * @return QuestionHint
     * @throws AsqException
     */
    public function getHintById(string $id) : QuestionHint
    {
        foreach ($this->hints as $hint) {
            if ($hint->getId() === $id) {
                return $hint;
            }
        }

        throw new AsqException(sprintf("Hint with Id: %s does not exist", $id));
    }


    /**
     * @param string $json_data
     *
     * @return QuestionHints
     * @throws AsqException
     */
    public static function deserialize(string $json_data) : QuestionHints
    {
        $data = json_decode($json_data);
        $hints = [];

        foreach ($data as $hint) {
            $hints[] = QuestionHint::create($hint->id, $hint->content, $hint->point_deduction);
        }

        return new QuestionHints($hints);
    }

    /**
     * @param QuestionHints $other
     *
     * @return bool
     */
    public function equals(QuestionHints $other) : bool
    {
        return !is_null($other)
            && count($this->hints) === count($other->hints)
            && $this->hintsAreEqual($other);
    }


    public function hintsAreEqual(QuestionHints $other) : bool
    {
        /** @var QuestionHint $my_hint */
        foreach ($this->hints as $my_hint) {
            $found = false;

            /** @var QuestionHint $other_hint */
            foreach ($other->hints as $other_hint) {
                if ($my_hint->equals($other_hint)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return false;
            }
        }

        return true;
    }
}
