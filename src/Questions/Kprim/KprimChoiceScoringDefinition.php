<?php
declare(strict_types = 1);
namespace srag\asq\Questions\Kprim;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;

/**
 * Class KprimChoiceScoringDefinition
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 * @package srag/asq
 * @author Adrian Lüthi <al@studer-raimann.ch>
 */
class KprimChoiceScoringDefinition extends AnswerDefinition
{

    const VAR_KPSD_CORRECT = 'kpsd_correct';

    const STR_TRUE = "True";

    const STR_FALSE = "False";

    /**
     * @var bool
     */
    protected $correct_value;

    /**
     * @param bool $correct_value
     * @return KprimChoiceScoringDefinition
     */
    public static function create(bool $correct_value) : KprimChoiceScoringDefinition
    {
        $object = new KprimChoiceScoringDefinition();
        $object->correct_value = $correct_value;
        return $object;
    }

    /**
     * @return boolean
     */
    public function isCorrectValue()
    {
        return $this->correct_value;
    }

    /**
     * @return array
     */
    public static function getFields(QuestionPlayConfiguration $play) : array
    {
        global $DIC;

        /** @var $conf KprimChoiceEditorConfiguration */
        $conf = $play->getEditorConfiguration();

        $fields = [];
        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_options'), 
            AsqTableInputFieldDefinition::TYPE_RADIO, 
            self::VAR_KPSD_CORRECT, 
            [
                empty($conf->getLabelTrue()) ? 
                    $DIC->language()->txt('asq_label_right') : 
                    $conf->getLabelTrue() => self::STR_TRUE,
                empty($conf->getLabelFalse()) ? 
                    $DIC->language()->txt('asq_label_wrong') : 
                    $conf->getLabelFalse() => self::STR_FALSE
            ]
        );

        return $fields;
    }

    /**
     * {@inheritdoc}
     * @see \srag\asq\Domain\Model\Answer\Option\AnswerDefinition::getValues()
     */
    public function getValues(): array
    {
        return [
            self::VAR_KPSD_CORRECT => $this->correct_value ? self::STR_TRUE : self::STR_FALSE
        ];
    }

    public static function getValueFromPost(string $index)
    {
        return KprimChoiceScoringDefinition::create($_POST[self::getPostKey($index, self::VAR_KPSD_CORRECT)] === self::STR_TRUE);
    }
}