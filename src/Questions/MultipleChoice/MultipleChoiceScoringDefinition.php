<?php
declare(strict_types=1);

namespace srag\asq\Questions\MultipleChoice;

use stdClass;
use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

/**
 * Class MultipleChoiceScoringDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class MultipleChoiceScoringDefinition extends AnswerDefinition {

	const VAR_MCSD_SELECTED = 'mcsd_selected';
	const VAR_MCSD_UNSELECTED = 'mcsd_unselected';

	/**
	 * @var float
	 */
	protected $points_selected;
	/**
	 * @var float
	 */
	protected $points_unselected;


	/**
	 * MultipleChoiceScoringDefinition constructor.
	 *
	 * @param int $points_selected
	 * @param int $points_unselected
	 */
	public function __construct(float $points_selected = 0.0, float $points_unselected = 0.0)
	{
		$this->points_selected = $points_selected;
		$this->points_unselected = $points_unselected;
	}


	/**
	 * @return int
	 */
	public function getPointsSelected(): float {
		return $this->points_selected;
	}

	/**
	 * @return int
	 */
	public function getPointsUnselected(): float {
		return $this->points_unselected;
	}

	public static function getFields(QuestionPlayConfiguration $play): array {
	    global $DIC;
	    
	    $fields = [];
	    $fields[self::VAR_MCSD_SELECTED] = new AsqTableInputFieldDefinition(
		    $DIC->language()->txt('asq_label_checked'),
	        AsqTableInputFieldDefinition::TYPE_NUMBER,
			self::VAR_MCSD_SELECTED
		);

	    $fields[self::VAR_MCSD_UNSELECTED] = new AsqTableInputFieldDefinition(
		    $DIC->language()->txt('asq_label_unchecked'),
	        AsqTableInputFieldDefinition::TYPE_NUMBER,
			self::VAR_MCSD_UNSELECTED
		);

		return $fields;
	}

	public static function getValueFromPost(string $index) {
		return new MultipleChoiceScoringDefinition(
		    floatval($_POST[self::getPostKey($index, self::VAR_MCSD_SELECTED)]),
		    floatval($_POST[self::getPostKey($index, self::VAR_MCSD_UNSELECTED)])
		);
	}

	public function getValues(): array {
		return [self::VAR_MCSD_SELECTED => $this->points_selected, 
		        self::VAR_MCSD_UNSELECTED => $this->points_unselected];
	}


	public static function deserialize(stdClass $data) {
		return new MultipleChoiceScoringDefinition(
			$data->points_selected,
			$data->points_unselected
		);
	}
	
	/**
	 * @var string
	 */
	private static $error_message;
	
	/**
	 * @param string $index
	 * @return bool
	 */
	public static function checkInput(int $count) : bool {
	    global $DIC;
	    
	    $points_found = false;
	    
	    for ($i = 1; $i <= $count; $i++) {
	        $str_i = strval($i);
    	    // unselected key does not exist in singlechoicequestion legacyform
	        if (!is_numeric($_POST[self::getPostKey($str_i, self::VAR_MCSD_SELECTED)]) ||
	            (array_key_exists(self::getPostKey($str_i, self::VAR_MCSD_UNSELECTED), $_POST) && 
	                !is_numeric($_POST[self::getPostKey($str_i, self::VAR_MCSD_UNSELECTED)]))) 
    	    {
    	        self::$error_message = $DIC->language()->txt('asq_error_numeric');
    	        return false;
    	    }
    	    
    	    if (intval($_POST[self::getPostKey($str_i, self::VAR_MCSD_SELECTED)]) > 0 ||
    	        (array_key_exists(self::getPostKey($str_i, self::VAR_MCSD_UNSELECTED), $_POST) && 
    	            intval($_POST[self::getPostKey($str_i, self::VAR_MCSD_UNSELECTED)]) > 0)) 
    	    {
    	        $points_found = true;
    	    }
	    }
	    
	    if (!$points_found) {
	        self::$error_message = $DIC->language()->txt('asq_error_points');;
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * @return string
	 */
	public static function getErrorMessage() : string {
	    return self::$error_message;
	}
}