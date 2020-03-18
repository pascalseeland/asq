<?php
declare(strict_types=1);

namespace srag\asq\Questions\TextSubset;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class TextSubsetScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class TextSubsetScoringConfiguration extends AbstractConfiguration {
    /**
     * @var ?int
     */
    protected $text_matching;
    
    static function create(?int $text_matching = null) : TextSubsetScoringConfiguration
    {
        $object = new TextSubsetScoringConfiguration();
        $object->text_matching = $text_matching;
        return $object;
    }
    
    /**
     * @return ?int
     */
    public function getTextMatching() : ?int
    {
        return $this->text_matching;
    }
    
    public function equals(AbstractValueObject $other): bool
    {
        /** @var TextSubsetScoringConfiguration $other */
        return get_class($this) === get_class($other) &&
               $this->text_matching === $other->text_matching;
    }
}