<?php
declare(strict_types=1);

namespace srag\asq\Domain\Model;

use InvalidArgumentException;

/**
 * Class ContentEditingMode
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ContentEditingMode
{
    const RTE_TEXTAREA = 'rte_textarea';
    const PAGE_OBJECT = 'page_object';

    /**
     * @var string
     */
    private $mode;


    /**
     * ContentEditingMode constructor.
     *
     * @param string $mode
     */
    public function __construct(string $mode)
    {
        switch($mode)
        {
            case self::RTE_TEXTAREA:
            case self::PAGE_OBJECT:

                $this->mode = $mode;
                break;

            default: throw new InvalidArgumentException(
                'invalid content editing mode given: '.$mode
            );
        }
    }

    /**
     * @return bool
     */
    public function isRteTextarea() : bool
    {
        return $this->mode == ContentEditingMode::RTE_TEXTAREA;
    }
    
    
    /**
     * @return bool
     */
    public function isPageObject() : bool
    {
        return $this->mode == ContentEditingMode::PAGE_OBJECT;
    }

    /**
     * @return string
     */
    public function getMode() : string
    {
        return $this->mode;
    }
}