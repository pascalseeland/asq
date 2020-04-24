<?php
declare(strict_types = 1);
namespace srag\asq\Questions\ErrorText;

use ilNumberInputGUI;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Question;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\UserInterface\Web\InputHelper;

/**
 * Class ErrorTextScoring
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class ErrorTextScoring extends AbstractScoring
{

    const VAR_POINTS_WRONG = 'ets_points_wrong';

    /**
     * {@inheritdoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::score()
     */
    function score(Answer $answer) : float
    {
        $reached_points = 0.0;

        $selected_words = $answer->getSelectedWordIndexes();
        $correct_words = [];

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ErrorTextScoringDefinition $scoring_definition */
            $scoring_definition = $option->getScoringDefinition();

            if (in_array($scoring_definition->getWrongWordIndex(), $selected_words)) {
                // multiple words '(( ))'
                if ($scoring_definition->getWrongWordLength() > 1) {
                    $correct = true;

                    for ($i = 0; $i < $scoring_definition->getWrongWordLength(); $i ++) {

                        $current = $scoring_definition->getWrongWordIndex() + $i;

                        if (! in_array($current, $selected_words)) {
                            $correct = false;
                            break;
                        }
                    }

                    if ($correct) {
                        for ($i = 0; $i < $scoring_definition->getWrongWordLength(); $i ++) {
                            $correct_words[] = $scoring_definition->getWrongWordIndex() + $i;
                        }
                        $reached_points += $scoring_definition->getPoints();
                    }
                } // single word '#'
                else {
                    $correct_words[] = $scoring_definition->getWrongWordIndex();
                    $reached_points += $scoring_definition->getPoints();
                }
            }
        }

        // deduct wrong selections
        $reached_points -= count(array_diff($selected_words, $correct_words));

        return $reached_points;
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::calculateMaxScore()
     */
    protected function calculateMaxScore() : float
    {
        $max_score = 0.0;

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ErrorTextScoringDefinition $scoring_definition */
            $scoring_definition = $option->getScoringDefinition();
            $max_score += $scoring_definition->getPoints();
        }

        return $max_score;
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::getBestAnswer()
     */
    public function getBestAnswer() : Answer
    {
        $selected_word_indexes = [];

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var ErrorTextScoringDefinition $scoring_definition */
            $scoring_definition = $option->getScoringDefinition();

            for ($i = 0; $i < $scoring_definition->getWrongWordLength(); $i ++) {

                $selected_word_indexes[] = $scoring_definition->getWrongWordIndex() + $i;
            }
        }

        return ErrorTextAnswer::create($selected_word_indexes);
    }

    /**
     * @return array|null
     */
    public static function generateFields(?AbstractConfiguration $config, AnswerOptions $options = null) : ?array
    {
        /** @var ErrorTextScoringConfiguration $config */
        global $DIC;

        $fields = [];

        $points_wrong = new ilNumberInputGUI($DIC->language()->txt('asq_label_points_wrong'), self::VAR_POINTS_WRONG);
        $points_wrong->setSize(6);
        $points_wrong->setRequired(true);
        $points_wrong->setMaxValue(0);
        $points_wrong->setInfo($DIC->language()
            ->txt('asq_info_points_wrong'));
        $fields[self::VAR_POINTS_WRONG] = $points_wrong;

        if ($config !== null) {
            $points_wrong->setValue($config->getPointsWrong());
        }

        return $fields;
    }

    /**
     * @return ?AbstractConfiguration|null
     */
    public static function readConfig() : ?AbstractConfiguration
    {
        return ErrorTextScoringConfiguration::create(InputHelper::readFloat(self::VAR_POINTS_WRONG));
    }

    /**
     * @param Question $question
     * @return bool
     */
    public static function isComplete(Question $question) : bool
    {
        /** @var ErrorTextScoringConfiguration $config */
        $config = $question->getPlayConfiguration()->getScoringConfiguration();

        if (empty($config->getPointsWrong())) {
            return false;
        }

        if (count($question->getAnswerOptions()->getOptions()) < 1) {
            return false;
        }

        foreach ($question->getAnswerOptions()->getOptions() as $option) {
            /** @var ErrorTextScoringDefinition $option_config */
            $option_config = $option->getScoringDefinition();

            if (empty($option_config->getPoints()) || empty($option_config->getWrongWordIndex() || empty($option_config->getWrongWordLength()))) {
                return false;
            }
        }

        return true;
    }
}