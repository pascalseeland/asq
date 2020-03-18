<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Scoring;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;

/**
 * Class EmptyScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EmptyScoringDefinition extends AnswerDefinition {
    
    public static function getFields(QuestionPlayConfiguration $play): array {
        return [];
    }
    
    public static function getValueFromPost($index) {
        return new EmptyScoringDefinition();
    }
    
    public function getValues(): array {
        return [];
    }
    
    
    public static function deserialize($data) {
        return new EmptyScoringDefinition();
    }
}