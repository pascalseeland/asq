<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching;

use srag\asq\Domain\Model\Answer\Answer;

/**
 * Class MatchingAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingAnswer extends Answer {
    /**
     * @var string[]
     */
    protected $matches;
    
    public static function create(?array $matches) : MatchingAnswer {
        $object = new MatchingAnswer();
        $object->matches = $matches;
        return $object;
    }
    
    public function getMatches() : ?array {
        return $this->matches;
    }
    
    public function getAnswerString() : string {
        return implode(';', $this->matches);
    }
}