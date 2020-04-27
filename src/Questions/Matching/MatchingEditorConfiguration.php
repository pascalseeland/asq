<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching;

use srag\asq\Domain\Model\AbstractConfiguration;

/**
 * Class MultipleChoiceEditorConfiguration
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingEditorConfiguration extends AbstractConfiguration {

    const SHUFFLE_NONE = 0;
    const SHUFFLE_DEFINITIONS = 1;
    const SHUFFLE_TERMS = 2;
    const SHUFFLE_BOTH = 3;

    /**
     * @var ?int
     */
    protected $shuffle;

    /**
     * @var ?int
     */
    protected $thumbnail_size;

    const MATCHING_ONE_TO_ONE = 0;
    const MATCHING_MANY_TO_ONE = 1;
    const MATCHING_MANY_TO_MANY = 2;

    /**
     * @var ?int
     */
    protected $matching_mode;

    /**
     * @var ?array
     */
    protected $definitions;

    /**
     * @var ?array
     */
    protected $terms;

    /**
     * @var ?array
     */
    protected $matches;

    /**
     * @param int $shuffle
     * @param int $thumbnail_size
     * @param int $matching_mode
     * @param array $definitions
     * @param array $terms
     * @param array $matches
     * @return MatchingEditorConfiguration
     */
    public static function create(
            int $shuffle = self::SHUFFLE_NONE,
            int $thumbnail_size = 100,
            int $matching_mode = self::MATCHING_ONE_TO_ONE,
            array $definitions = [],
            array $terms = [],
            array $matches = []
        ) : MatchingEditorConfiguration
    {
        $object = new MatchingEditorConfiguration();
        $object->shuffle = $shuffle;
        $object->thumbnail_size = $thumbnail_size;
        $object->matching_mode = $matching_mode;
        $object->definitions = $definitions;
        $object->terms = $terms;
        $object->matches = $matches;
        return $object;
    }

    /**
     * @return ?int
     */
    public function getShuffle() : ?int
    {
        return $this->shuffle;
    }

    /**
     * @return bool
     */
    public function isShuffleDefinitions() : bool
    {
        return $this->shuffle === self::SHUFFLE_DEFINITIONS ||
               $this->shuffle === self::SHUFFLE_BOTH;
    }

    /**
     * @return bool
     */
    public function isShuffleTerms() : bool
    {
        return $this->shuffle === self::SHUFFLE_TERMS ||
               $this->shuffle === self::SHUFFLE_BOTH;
    }

    /**
     * @return ?int
     */
    public function getThumbnailSize() : ?int
    {
        return $this->thumbnail_size;
    }

    /**
     * @return ?int
     */
    public function getMatchingMode() : ?int
    {
        return $this->matching_mode;
    }

    /**
     * @return ?array
     */
    public function getDefinitions() : ?array
    {
        return $this->definitions;
    }

    /**
     * @return ?array
     */
    public function getTerms() : ?array
    {
        return $this->terms;
    }

    /**
     * @return ?array
     */
    public function getMatches() : ?array
    {
        return $this->matches;
    }
}