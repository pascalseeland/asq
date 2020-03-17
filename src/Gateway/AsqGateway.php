<?php
declare(strict_types=1);

namespace ILIAS\AssessmentQuestion\Gateway;

/**
 * Class AsqGateway
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 * @author  Björn Heyser <bh@bjoernheyser.de>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 *
 * @package ILIAS\Services\AssessmentQuestion\PublicApi\Factory
 */
class AsqGateway
{
    /**
     * @var AsqGateway
     */
    private static $instance;
    
    private function __construct() { }
    
    /**
     * @return AsqGateway
     */
    public static function get() : AsqGateway {
        if (is_null(self::$instance)) {
            self::$instance = new AsqGateway();
        }
        
        return self::$instance;
    }
    
    /**
     * @var QuestionService
     */
    private $question_service;
    
    /**
     * @return QuestionService
     */
    public function question() {
        if (is_null($this->question_service)) {
            $this->question_service = new QuestionService();
        }
        
        return $this->question_service;
    }
    
    /**
     * @var AnswerService
     */
    private $answer_service;
    
    /**
     * @return AnswerService
     */
    public function answer() {
        if (is_null($this->answer_service)) {
            $this->answer_service = new AnswerService();
        }
        
        return $this->answer_service;
    }
    
    /**
     * @var LinkService
     */
    private $link_service;
    
    /**
     * @return LinkService
     */
    public function link() {
        if (is_null($this->link_service)) {
            $this->link_service = new LinkService();
        }
        
        return $this->link_service;
    }
    
    /**
     * @var UIService
     */
    private $ui_service;
    
    /**
     * @return UIService
     */
    public function link() {
        if (is_null($this->ui_service)) {
            $this->ui_service = new UIService();
        }
        
        return $this->ui_service;
    }
}