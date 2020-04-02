<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model\Answer\Option;

use srag\asq\Domain\Model\QuestionPlayConfiguration;

/**
 * Class EmptyDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EmptyDefinition extends AnswerDefinition {
    public static function create() : EmptyDefinition {
        return new EmptyDefinition();
    }
    
    public static function getFields(QuestionPlayConfiguration $play): array {
        return [];
    }
    
    public static function getValueFromPost($index) {
        return EmptyDefinition::create();
    }
    
    public function getValues(): array {
        return [];
    }
}