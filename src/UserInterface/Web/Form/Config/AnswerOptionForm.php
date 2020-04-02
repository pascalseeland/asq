<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Form\Config;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerOption;
use srag\asq\Domain\Model\Answer\Option\AnswerOptions;
use srag\asq\UserInterface\Web\Fields\AsqTableInput;

/**
 * Class AnswerOptionForm
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class AnswerOptionForm extends AsqTableInput {
    const VAR_POST = 'answer_options';
    
    /**
     * @var ?AnswerOptions
     */
    private $options;
    /**
     * @var QuestionPlayConfiguration
     */
    private $configuration;
    
	public function __construct(string $title, 
	                            ?QuestionPlayConfiguration $configuration, 
	                            ?AnswerOptions $options, 
	                            ?array $definitions = null,
	                            ?array $form_configuration = null) 
	{
	    $this->setRequired(true);
	    $this->configuration = $configuration;
	    
	    if(is_null($definitions) && !is_null($configuration)) {
	        $definitions = $this->collectFields($configuration);
	    }
	    
	    if (is_null($form_configuration) && !is_null($configuration)) {
	        $form_configuration = $this->collectConfigurations($configuration);
	    }    
	    
		parent::__construct($title, 
		                    self::VAR_POST,
		                    !is_null($options) ? $this->getRawOptionValue($options->getOptions()) : [],
		                    $definitions,
		                    $form_configuration);
		
		$this->options = $options;
	}
	
	public function setAnswerOptions(AnswerOptions $options) {
	    $this->setValues($this->getRawOptionValue($options->getOptions()));
	}
	
	private function getRawOptionValue(array $options) {
	    return array_map(function($option) {
	       return !is_null($option) ? $option->rawValues() : null;
	    }, $options);
	}
	
	/**
	 * @param QuestionPlayConfiguration $configuration
	 */
	public function setConfiguration(QuestionPlayConfiguration $configuration) {
	    $this->configuration = $configuration;
	}
	
	/**
	 * @return bool
	 */
	public function checkInput() : bool {    
	    $count = intval($_POST[Answeroptionform::VAR_POST]);
	    
	    $sd_class = $this->configuration->getScoringConfiguration()->configurationFor()::getScoringDefinitionClass();
	    $dd_class = $this->configuration->getEditorConfiguration()->configurationFor()::getDisplayDefinitionClass();
	    
        if(!$dd_class::checkInput($count)) {
            $this->setAlert($dd_class::getErrorMessage());
            return false;
        }
        
        if(!$sd_class::checkInput($count)) {
            $this->setAlert($sd_class::getErrorMessage());
            return false;
        }
	    
	    return true;
	}

	/**
	 * @param QuestionPlayConfiguration $play
	 *
	 * @return AnswerOptions
	 */
	public function readAnswerOptions() {
	    $this->readValues();
	    
	    $sd_class = $this->configuration->getScoringConfiguration()->configurationFor()::getScoringDefinitionClass();
	    $dd_class = $this->configuration->getEditorConfiguration()->configurationFor()::getDisplayDefinitionClass();
	    
	    $count = intval($_POST[Answeroptionform::VAR_POST]);

	    $options = [];
	    for ($i = 1; $i <= $count; $i++) {
	        $options[] = AnswerOption::create(
	                strval($i),
	                $dd_class::getValueFromPost(strval($i)),
	                $sd_class::getValueFromPost(strval($i)));
	    }
	    
	    $this->options = AnswerOptions::create($options);
	    $this->values = $this->getRawOptionValue($options);
	}

	public function getAnswerOptions() : AnswerOptions {
	    return $this->options;
	}
	
	/**
	 * @param QuestionPlayConfiguration $play
	 *
	 * @return array
	 */
	private function collectFields(?QuestionPlayConfiguration $play) : array {
	    $sd_class = $play->getScoringConfiguration()->configurationFor()::getScoringDefinitionClass();
	    $dd_class = $play->getEditorConfiguration()->configurationFor()::getDisplayDefinitionClass();
	    
	    
	    return array_merge($dd_class::getFields($play), $sd_class::getFields($play));
	}

	/**
	 * @param QuestionPlayConfiguration $play
	 *
	 * @return array
	 */
	private function collectConfigurations(QuestionPlayConfiguration $play) : array {
	    return array_merge($play->getEditorConfiguration()->getOptionFormConfig(), 
	                       $play->getScoringConfiguration()->getOptionFormConfig());
	}
}