<?php

/**
 * Class AsqPageObjectConfig
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Martin Studer <ms@studer-raimann.ch>
 */
class AsqPageObjectConfig extends \ilPageConfig
{
    public function init()
    {
        $this->setEnablePCType('Tabs', true);
        $this->setEnableInternalLinks(false);
    }
}
