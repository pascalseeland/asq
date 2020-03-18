<?php
declare(strict_types=1);

namespace srag\asq\Application\Service;

use srag\asq\Application\Exception\AsqException;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\Infrastructure\Persistence\SimpleStoredAnswer;

/**
 * Class AnswerService
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AnswerService extends ASQService {
    /**
     * @param QuestionDto $question
     * @param Answer $answer
     * @return float
     */
    public function getScore(QuestionDto $question, Answer $answer) : float {
        $scoring_class = $question->getPlayConfiguration()->getScoringConfiguration()->configurationFor();
        /** @var $scoring AbstractScoring */
        $scoring = new $scoring_class($question);
        return $scoring->score($answer);
    }
    
    /**
     * @param QuestionDto $question
     * @return float
     */
    public function getMaxScore(QuestionDto $question) : float {
        $scoring_class = $question->getPlayConfiguration()->getScoringConfiguration()->configurationFor();
        /** @var $scoring AbstractScoring */
        $scoring = new $scoring_class($question);
        return $scoring->getMaxScore();
    }
    
    /**
     * @param QuestionDto $question
     * @return Answer
     */
    public function getBestAnswer(QuestionDto $question) : Answer {
        $scoring_class = $question->getPlayConfiguration()->getScoringConfiguration()->configurationFor();
        /** @var $scoring AbstractScoring */
        $scoring = new $scoring_class($question);
        return $scoring->getBestAnswer();
    }
    
    /**
     * @param Answer $answer
     * @param string $uuid
     * @return string
     */
    public function storeAnswer(Answer $answer, ?string $uuid = null) : string {
        $stored = SimpleStoredAnswer::createNew($answer, $uuid);
        $stored->create();
        return $stored->getUuid();
    }
    
    /**
     * @param string $uuid
     * @param int $version
     * @return Answer
     */
    public function getAnswer(string $uuid, ?int $version = null) : Answer {
        if (is_null($version)) {
            $stored = SimpleStoredAnswer::where(['uuid' => $uuid])->orderBy('version', 'DESC')->first();
        }
        else {
            $stored = SimpleStoredAnswer::where(['uuid' => $uuid, 'version' => $version])->first();
        }
        
        if (is_null($stored)) {
            throw new AsqException(sprintf('The requested Answer does not exist UUID = "%s" Version = "%s"', $uuid, $version));
        }
        
        return $stored->getAnswer();
    }
    
    public function getAnswerHistory(string $uuid) : array {
        $history = SimpleStoredAnswer::where(['uuid' => $uuid])->get();
        
        $answers = [];
        
        foreach($history as $stored_answer) {
            $answers[$stored_answer->getVersion()] = $stored_answer->getAnswer();
        }
        
        return $answers;
    }
}