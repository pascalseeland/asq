<?php
declare(strict_types=1);

namespace srag\asq\Questions\Numeric;

use ilFormSectionHeaderGUI;
use ilNumberInputGUI;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Answer\Option\EmptyDefinition;
use srag\asq\Domain\Model\Scoring\AbstractScoring;

/**
 * Class NumericScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class NumericScoring extends AbstractScoring
{
    const VAR_POINTS = 'ns_points';
    const VAR_LOWER_BOUND = 'ns_lower_bound';
    const VAR_UPPER_BOUND = 'ns_upper_bound';

    function score(Answer $answer) : float
    {
        $reached_points = 0;

        /** @var NumericScoringConfiguration $scoring_conf */
        $scoring_conf = $this->question->getPlayConfiguration()->getScoringConfiguration();

        $float_answer = $answer->getValue();

        if ($float_answer !== null &&
            $scoring_conf->getLowerBound() <= $float_answer &&
            $scoring_conf->getUpperBound() >= $float_answer) {
            $reached_points = $scoring_conf->getPoints();
        }

        return $reached_points;
    }

    protected function calculateMaxScore() : float
    {
        return $this->question->getPlayConfiguration()->getScoringConfiguration()->getPoints();
    }
    
    public function getBestAnswer(): Answer
    {
        /** @var NumericScoringConfiguration $conf */
        $conf = $this->question->getPlayConfiguration()->getScoringConfiguration();
        
        return NumericAnswer::create(($conf->getUpperBound() + $conf->getLowerBound()) / 2);
    }
    
    /**
     * @param AbstractConfiguration|null $config
     *
     * @return array|null
     */
    public static function generateFields(?AbstractConfiguration $config, AnswerOptions $options = null): ?array {
        /** @var NumericScoringConfiguration $config */
        global $DIC;
        
        $fields = [];

        $points = new ilNumberInputGUI($DIC->language()->txt('asq_label_points'), self::VAR_POINTS);
        $points->setRequired(true);
        $points->setSize(2);
        $fields[self::VAR_POINTS] = $points;

        $spacer = new ilFormSectionHeaderGUI();
        $spacer->setTitle($DIC->language()->txt('asq_range'));
        $fields[] = $spacer;
        
        $lower_bound = new ilNumberInputGUI($DIC->language()->txt('asq_label_lower_bound'), self::VAR_LOWER_BOUND);
        $lower_bound->setRequired(true);
        $lower_bound->allowDecimals(true);
        $lower_bound->setSize(6);
        $fields[self::VAR_LOWER_BOUND] = $lower_bound;

        $upper_bound = new ilNumberInputGUI($DIC->language()->txt('asq_label_upper_bound'), self::VAR_UPPER_BOUND);
        $upper_bound->setRequired(true);
        $upper_bound->allowDecimals(true);
        $upper_bound->setSize(6);
        $fields[self::VAR_UPPER_BOUND] = $upper_bound;

        if ($config !== null) {
            $points->setValue($config->getPoints());
            $lower_bound->setValue($config->getLowerBound());
            $upper_bound->setValue($config->getUpperBound());
        }

        return $fields;
    }

    public static function readConfig()
    {
        return NumericScoringConfiguration::create(
            intval($_POST[self::VAR_POINTS]),
            floatval($_POST[self::VAR_LOWER_BOUND]),
            floatval($_POST[self::VAR_UPPER_BOUND]));
    }
    
    /**
     * @return string
     */
    public static function getScoringDefinitionClass(): string {
        return EmptyDefinition::class;
    }
    
    public static function isComplete(Question $question): bool
    {
        /** @var NumericScoringConfiguration $config */
        $config = $question->getPlayConfiguration()->getScoringConfiguration();
        
        if (empty($config->getPoints()) ||
            empty($config->getLowerBound() ||
            empty($config->getUpperBound()))) 
        {
            return false;
        }
        
        return true;
    }
}