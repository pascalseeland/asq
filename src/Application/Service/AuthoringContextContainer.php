<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use ILIAS\UI\Component\Link\Standard as UiStandardLink;

/**
 * Class AuthoringContextContainer
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AuthoringContextContainer
{
    /**
     * @var UiStandardLink
     */
    protected $backLink;

    /**
     * @var int
     */
    protected $refId;

    /**
     * @var int
     */
    protected $objId;

    /**
     * @var string
     */
    protected $objType;

    /**
     * @var int
     */
    protected $actorId;

    /**
     * @var bool
     */
    protected $writeAccess;

    /**
     * @var array
     */
    protected $afterQuestionCreationCtrlClassPath;

    /**
     * @var string
     */
    protected $afterQuestionCreationCtrlCommand;


    /**
     * AuthoringContextContainer constructor.
     *
     * @param UiStandardLink $backLink
     * @param int            $refId
     * @param int            $objId
     * @param string         $objType
     * @param int            $actorId
     * @param bool           $writeAccess
     */
    public function __construct(
        UiStandardLink $backLink,
        int $refId,
        int $objId,
        string $objType,
        int $actorId,
        bool $writeAccess
    )
    {
        $this->backLink = $backLink;
        $this->refId = $refId;
        $this->objId = $objId;
        $this->objType = $objType;
        $this->actorId = $actorId;
        $this->writeAccess = $writeAccess;
    }


    /**
     * @return UiStandardLink
     */
    public function getBackLink() : UiStandardLink
    {
        return $this->backLink;
    }


    /**
     * @return int
     */
    public function getRefId() : int
    {
        return $this->refId;
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->objId;
    }


    /**
     * @return string
     */
    public function getObjType() : string
    {
        return $this->objType;
    }


    /**
     * @return int
     */
    public function getActorId() : int
    {
        return $this->actorId;
    }


    /**
     * @return bool
     */
    public function hasWriteAccess() : bool
    {
        return $this->writeAccess;
    }
}
