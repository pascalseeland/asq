<?php
declare(strict_types=1);

namespace srag\asq\Questions\Formula;

use ILIAS\UI\NotImplementedException;
use EvalMath;
use Exception;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilTextInputGUI;
use srag\asq\Domain\QuestionDto;
use srag\asq\Domain\Model\AbstractConfiguration;
use srag\asq\Domain\Model\Answer\Answer;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\Domain\Model\Scoring\AbstractScoring;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\InputHelper;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

/**
 * Class FormulaScoring
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class FormulaScoring extends AbstractScoring {
    const VAR_UNITS = 'fs_units';
    const VAR_PRECISION = 'fs_precision';
    const VAR_TOLERANCE = 'fs_tolerance';
    const VAR_RESULT_TYPE = 'fs_type';
    const VAR_VARIABLES = 'fs_variables';

    /**
     * @var AsqTableInput
     */
    private static $variables_table;

    /**
     * @var FormulaScoringConfiguration
     */
    protected $configuration;

    /**
     * @param QuestionDto $question
     */
    public function __construct($question)
    {
        parent::__construct($question);

        $this->configuration = $question->getPlayConfiguration()->getScoringConfiguration();
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::score()
     */
    public function score(Answer $answer) : float
    {
        $reached_points = 0.0;

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var $result FormulaScoringDefinition */
            $result = $option->getScoringDefinition();

            $answers = $answer->getValues();
            $formula = $result->getFormula();

            foreach($answers as $key => $value) {
                $formula = str_replace($key, $value, $formula);
            }

            $math = new EvalMath();

            $result_expected = $math->evaluate($formula);

            $result_given = null;
            $raw_result = $answers['$r' . $option->getOptionId()];
            $unit_given = $answers['$r' . $option->getOptionId() . FormulaEditor::VAR_UNIT] ?? '';

            //get decimal value of answer if allowed
            if (($this->configuration->getResultType() === FormulaScoringConfiguration::TYPE_ALL ||
                $this->configuration->getResultType() === FormulaScoringConfiguration::TYPE_DECIMAL) &&
                is_numeric($raw_result))
            {
                $result_given = floatval($raw_result);
            }

            //get compound result if no value yet and it is allowed
            if (is_null($result_given) &&
                $this->configuration->getResultType() !== FormulaScoringConfiguration::TYPE_DECIMAL &&
                strpos($raw_result, '/'))
            {
                $split = explode('/', $raw_result);
                $numerator = floatval($split[0]);
                $denominator = floatval($split[1]);

                $result_given = $numerator / $denominator;

                // invalidate result if not coprime and option is set
                if ($this->configuration->getResultType() === FormulaScoringConfiguration::TYPE_COPRIME_FRACTION &&
                    $this->greatest_common_divisor($numerator, $denominator) !== 1)
                {
                    $result_given = null;
                }
            }

            if (!is_null($result_given)) {
                $difference = abs($result_expected - $result_given);
                $max_allowed_difference = $result_expected / 100 * $this->configuration->getTolerance();

                if ($difference <= $max_allowed_difference &&
                    $unit_given === $result->getUnit()) {
                    $reached_points += $result->getPoints();
                }
            }
        }

        return $reached_points;
    }

    /**
     * Euclids gcd algorithm
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    private function greatest_common_divisor(int $a, int $b) : int
    {
        return ($a % $b) ? $this->greatest_common_divisor($b,$a % $b) : $b;
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::getBestAnswer()
     */
    public function getBestAnswer() : Answer
    {
        //TODO result debending on random number
        throw new NotImplementedException("implement FormulaScoring->getBestAnswer()");
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Scoring\AbstractScoring::calculateMaxScore()
     */
    protected function calculateMaxScore() : float
    {
        $max_score = 0.0;

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            $max_score += $option->getScoringDefinition()->getPoints();
        }

        return $max_score;
    }

    /**
     * @return array
     */
    public static function generateFields(?AbstractConfiguration $config, AnswerOptions $options = null): array
    {
        global $DIC;

        /** @var $config FormulaScoringConfiguration */

        $fields = [];

        $units = new ilTextInputGUI($DIC->language()->txt('asq_label_units'), self::VAR_UNITS);
        $units->setInfo($DIC->language()->txt('asq_description_units'));
        $fields[self::VAR_UNITS] = $units;

        $precision = new ilNumberInputGUI($DIC->language()->txt('asq_label_precision'), self::VAR_PRECISION);
        $precision->setInfo($DIC->language()->txt('asq_description_precision'));
        $precision->setRequired(true);
        $fields[self::VAR_PRECISION] = $precision;

        $tolerance = new ilNumberInputGUI($DIC->language()->txt('asq_label_tolerance'), self::VAR_TOLERANCE);
        $tolerance->setInfo($DIC->language()->txt('asq_description_tolerance'));
        $tolerance->setSuffix('%');
        $fields[self::VAR_TOLERANCE] = $tolerance;

        $result_type = new ilRadioGroupInputGUI($DIC->language()->txt('asq_label_result_type'), self::VAR_RESULT_TYPE);
        $result_type->setRequired(true);
        $result_type->addOption(new ilRadioOption($DIC->language()->txt('asq_label_result_all'),
                                                  FormulaScoringConfiguration::TYPE_ALL,
                                                  $DIC->language()->txt('asq_description_result_all')));
        $result_type->addOption(new ilRadioOption($DIC->language()->txt('asq_label_result_decimal'),
                                                  FormulaScoringConfiguration::TYPE_DECIMAL,
                                                  $DIC->language()->txt('asq_description_result_decimal')));
        $result_type->addOption(new ilRadioOption($DIC->language()->txt('asq_label_result_fraction'),
                                                  FormulaScoringConfiguration::TYPE_FRACTION,
                                                  $DIC->language()->txt('asq_description_result_fraction')));
        $result_type->addOption(new ilRadioOption($DIC->language()->txt('asq_label_result_coprime_fraction'),
                                                  FormulaScoringConfiguration::TYPE_COPRIME_FRACTION,
                                                  $DIC->language()->txt('asq_description_result_coprime_fraction')));
        $fields[self::VAR_RESULT_TYPE] = $result_type;

        self::$variables_table = new AsqTableInput(
            $DIC->language()->txt('asq_label_variables'),
            self::VAR_VARIABLES,
            $config->getVariablesArray(),
            [
                new AsqTableInputFieldDefinition(
                    $DIC->language()->txt('asq_header_min'),
                    AsqTableInputFieldDefinition::TYPE_TEXT,
                    FormulaScoringVariable::VAR_MIN),
                new AsqTableInputFieldDefinition(
                    $DIC->language()->txt('asq_header_max'),
                    AsqTableInputFieldDefinition::TYPE_TEXT,
                    FormulaScoringVariable::VAR_MAX),
                new AsqTableInputFieldDefinition(
                    $DIC->language()->txt('asq_header_unit'),
                    AsqTableInputFieldDefinition::TYPE_TEXT,
                    FormulaScoringVariable::VAR_UNIT),
                new AsqTableInputFieldDefinition(
                    $DIC->language()->txt('asq_header_multiple_of'),
                    AsqTableInputFieldDefinition::TYPE_TEXT,
                    FormulaScoringVariable::VAR_MULTIPLE_OF)
            ],
            [
                AsqTableInput::OPTION_HIDE_ADD_REMOVE => true
            ]);
        self::$variables_table->setRequired(true);
        $fields[self::VAR_VARIABLES] = self::$variables_table;

        if ($config !== null) {

            $units->setValue($config->getUnitString());
            $precision->setValue($config->getPrecision());
            $tolerance->setValue($config->getTolerance());
            $result_type->setValue($config->getResultType());
        }
        else {
            $tolerance->setValue(0);
            $result_type->setValue(FormulaScoringConfiguration::TYPE_ALL);
        }

        return $fields;
    }

    /**
     * @return FormulaScoringConfiguration
     */
    public static function readConfig() : FormulaScoringConfiguration
    {
        $variables = [];
        $raw_variables = self::$variables_table->readValues();

        foreach ($raw_variables as $raw_variable) {
            $variables[] = FormulaScoringVariable::create(
                floatval($raw_variable[FormulaScoringVariable::VAR_MIN]),
                floatval($raw_variable[FormulaScoringVariable::VAR_MAX]),
                AsqHtmlPurifier::getInstance()->purify($raw_variable[FormulaScoringVariable::VAR_UNIT]),
                empty($raw_variable[FormulaScoringVariable::VAR_MULTIPLE_OF]) ?
                    null:
                    floatval($raw_variable[FormulaScoringVariable::VAR_MULTIPLE_OF]));
        }

        return FormulaScoringConfiguration::create(
            AsqHtmlPurifier::getInstance()->purify($_POST[self::VAR_UNITS]),
            InputHelper::readInt(self::VAR_PRECISION),
            InputHelper::readFloat(self::VAR_TOLERANCE),
            InputHelper::readInt(self::VAR_RESULT_TYPE),
            $variables
        );
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        if (is_null($this->configuration->getPrecision()) ||
            is_null($this->configuration->getResultType())) {
            return false;
        }

        foreach ($this->configuration->getVariables() as $var) {
            if (! $var->isComplete()) {
                return false;
            }

            if (! $this->isVarValid($var)) {
                return false;
            }
        }

        foreach ($this->question->getAnswerOptions()->getOptions() as $option) {
            /** @var FormulaScoringDefinition $option_config */
            $option_config = $option->getScoringDefinition();

            if (! $option_config->isComplete($this->configuration))
            {
                return false;
            }

            if (! $this->isResultValid($option_config)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param FormulaScoringVariable $var
     * @return bool
     */
    private function isVarValid(FormulaScoringVariable $var) : bool
    {
        if (! $this->inPrecision($var->getMax(), $this->configuration->getPrecision()) ||
            ! $this->inPrecision($var->getMin(), $this->configuration->getPrecision()) ||
            ! $this->inPrecision($var->getMultipleOf(), $this->configuration->getPrecision()))
        {
            return false;
        }

        if (! is_null($var->getUnit()) &&
            ! in_array($var->getUnit(), $this->configuration->getUnits()))
        {
            return false;
        }

        return true;
    }

    /**
     * @param int $precision
     * @param float $number
     * @return bool
     */
    private function inPrecision(float $number, int $precision) : bool
    {
        $mult = $number * (10 ** $precision);

        return ceil($mult) === floor($mult);
    }

    /**
     * @param FormulaScoringDefinition $var
     * @param FormulaScoringConfiguration $config
     * @return bool
     */
    private function isResultValid(FormulaScoringDefinition $result) : bool
    {
        if (! is_null($result->getUnit()) &&
            ! in_array($result->getUnit(), $this->configuration->getUnits()))
        {
            return false;
        }

        $variables = [];

        $i = 0;
        foreach ($this->configuration->getVariables() as $var) {
            $i += 1;
            $variables['$v' . $i] = $this->configuration->generateVariableValue($var);
        }

        $formula = $result->getFormula();

        foreach($variables as $key => $value) {
            $formula = str_replace($key, $value, $formula);
        }

        $math = new EvalMath();

        try {
            $math->evaluate($formula);
        } catch (Exception $e)
        {
            return false;
        }

        return true;
    }
}