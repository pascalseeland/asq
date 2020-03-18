<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay;


use srag\asq\Domain\Model\Answer\Answer;

/**
 * Class EssayAnswer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayAnswer extends Answer {
    /**
     * @var string
     */
    protected $text;
    
    public static function create(?string $text = null) : EssayAnswer {
        $object = new EssayAnswer();
        $object->text = $text;
        return $object;
    }
    
    public function getText() : ?string {
        return $this->text;
    }
}