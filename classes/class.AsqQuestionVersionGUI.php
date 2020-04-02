<?php
declare(strict_types=1);

use srag\asq\AsqGateway;

/**
 * Class AsqQuestionVersionGUI
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AsqQuestionVersionGUI
{
    
    const CMD_SHOW_VERSIONS = 'showVersions';
    
    /**
     * @var string
     */
    protected $question_id;
    
    public function __construct(string $question_id) 
    {
            $this->question_id = $question_id;
    }
    
    
    public function executeCommand()
    {
        //AsqGateway::get()->question()->createQuestionRevision('Alice', $this->question_id);
        $versions = AsqGateway::get()->question()->getAllRevisionsOfQuestion($this->question_id);
        
        $breakpoint = 1;
    }
}
