<?php
declare(strict_types=1);

namespace srag\asq\Questions\Matching;

use ilNumberInputGUI;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\UserInterface\Web\InputHelper;

/**
 * Class MultipleChoiceScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MatchingScoring extends AbstractScoring
{
    const VAR_WRONG_DEDUCTION = 'ms_wrong_deduction';

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::score()
     */
    public function score(Answer $answer) : float
    {
        $matches = [];
        $wrong_deduction = $this->question->getPlayConfiguration()->getScoringConfiguration()->getWrongDeduction();

        foreach ($this->question->getPlayConfiguration()->getEditorConfiguration()->getMatches() as $match) {
            $key = $match[MatchingEditor::VAR_MATCH_DEFINITION] . '-' . $match[MatchingEditor::VAR_MATCH_TERM];
            $matches[$key] = intval($match[MatchingEditor::VAR_MATCH_POINTS]);
        };

        $score = 0;

        foreach ($answer->getMatches() as $given_match) {
            if(array_key_exists($given_match, $matches)) {
                $score += $matches[$given_match];
            }
            else {
                $score -= $wrong_deduction;
            }
        }

        return $score;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::getBestAnswer()
     */
    public function getBestAnswer() : Answer
    {
        $matches = [];

        foreach ($this->question->getPlayConfiguration()->getEditorConfiguration()->getMatches() as $match) {
            $matches[] = $match[MatchingEditor::VAR_MATCH_DEFINITION] . '-' . $match[MatchingEditor::VAR_MATCH_TERM];
        };

        return new MatchingAnswer($matches);
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::calculateMaxScore()
     */
    protected function calculateMaxScore() : float
    {
        $max_score = 0;

        foreach ($this->question->getPlayConfiguration()->getEditorConfiguration()->getMatches() as $match) {
            $max_score += intval($match[MatchingEditor::VAR_MATCH_POINTS]);
        };

        return $max_score;
    }

    /**
     * @return ?array
     */
    public static function generateFields(?AbstractConfiguration $config, AnswerOptions $options = null) : ?array
    {
        global $DIC;

        $fields = [];

        $wrong_deduction = new ilNumberInputGUI($DIC->language()->txt('asq_label_wrong_deduction'), self::VAR_WRONG_DEDUCTION);
        $wrong_deduction->setSize(2);
        $fields[self::VAR_WRONG_DEDUCTION] = $wrong_deduction;

        if (!is_null($config)) {
            $wrong_deduction->setValue($config->getWrongDeduction());
        }

        return $fields;
    }

    /**
     * @return MatchingScoringConfiguration
     */
    public static function readConfig() : MatchingScoringConfiguration
    {
        return MatchingScoringConfiguration::create(InputHelper::readFloat(self::VAR_WRONG_DEDUCTION));
    }

    /**
     * @return string
     */
    public static function getScoringDefinitionClass() : string
    {
        return EmptyDefinition::class;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        return true;
    }
}