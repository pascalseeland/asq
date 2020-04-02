<?php
declare(strict_types=1);

namespace srag\asq\Questions\Essay;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;

/**
 * Class EssayScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class EssayScoringDefinition extends AnswerDefinition {
    const VAR_POINTS = 'esd_points';
    const VAR_TEXT = 'esd_text';
    
    /**
     * @var ?float
     */
    protected $points;
    
    /**
     * @var ?string;
     */
    protected $text;
    
    /**
     * @param string $text
     * @param float $points
     * @return EssayScoringDefinition
     */
    public static function create(?string $text, ?float $points) : EssayScoringDefinition {
        $object = new EssayScoringDefinition();
        $object->points = $points;
        $object->text = $text;
        return $object;
    }

    public function getPoints() : ?float
    {
        return $this->points;
    }
    
    public function getText()  : ?string
    {
        return $this->text;
    }

    public static function getFields(QuestionPlayConfiguration $play): array
    {
        // point values will be set by essayscoring directly
        return [];
    }

    public function getValues(): array
    {
        return [
            self::VAR_POINTS => $this->points,
            self::VAR_TEXT => $this->text
        ];
    }

    public static function getValueFromPost(string $index)
    {
        $pointkey = self::getPostKey($index, self::VAR_POINTS);
        
        return EssayScoringDefinition::create(
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_TEXT)]),
            array_key_exists($pointkey, $_POST) ? floatval($_POST[$pointkey]) : 0);            
    }
}