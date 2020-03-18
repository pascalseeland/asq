<?php
declare(strict_types=1);

namespace srag\asq\Questions\Cloze;

use srag\CQRS\Aggregate\AbstractValueObject;
use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class ClozeScoringConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ClozeScoringConfiguration extends AbstractConfiguration {
    public static function create() : ClozeScoringConfiguration {
        return new ClozeScoringConfiguration();
    }
    
    public function equals(AbstractValueObject $other): bool
    {
        return get_class($this) === get_class($other);
    }
}