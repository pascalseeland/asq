<?php
declare(strict_types=1);

namespace srag\asq\Questions\ImageMap;

use srag\asq\Domain\Model\QuestionPlayConfiguration;
use srag\asq\Domain\Model\Answer\Option\AnswerDefinition;
use srag\asq\UserInterface\Web\AsqHtmlPurifier;
use srag\asq\UserInterface\Web\Fields\AsqTableInputFieldDefinition;
use srag\asq\UserInterface\Web\InputHelper;

/**
 * Class ImageMapEditorDisplayDefinition
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ImageMapEditorDisplayDefinition extends AnswerDefinition {
    const VAR_TOOLTIP = 'imedd_tooltip';
    const VAR_TYPE = 'imedd_type';
    const VAR_COORDINATES = 'imedd_coordinates';

    const TYPE_RECTANGLE = 1;
    const TYPE_CIRCLE = 2;
    const TYPE_POLYGON = 3;

    /**
     * @var ?string
     */
    protected $tooltip;

    /**
     * @var ?int
     */
    protected $type;

    /**
     * @var ?string
     */
    protected $coordinates;

    /**
     * @param string $tooltip
     * @param int $type
     * @param string $coordinates
     */
    public static function create(?string $tooltip, ?int $type, ?string $coordinates) : ImageMapEditorDisplayDefinition {
        $object = new ImageMapEditorDisplayDefinition();
        $object->tooltip = $tooltip;
        $object->type = $type;
        $object->coordinates = $coordinates;
        return $object;
    }

    /**
     * @return string
     */
    public function getTooltip() : ?string
    {
        return $this->tooltip;
    }

    /**
     * @return int
     */
    public function getType() : ?int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getCoordinates() : ?string
    {
        return $this->coordinates;
    }

    /**
     * @param QuestionPlayConfiguration $play
     * @return array
     */
    public static function getFields(QuestionPlayConfiguration $play) : array {
        global $DIC;

        $fields = [];

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_tooltip'),
            AsqTableInputFieldDefinition::TYPE_TEXT,
            self::VAR_TOOLTIP
            );

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_type'),
            AsqTableInputFieldDefinition::TYPE_DROPDOWN,
            self::VAR_TYPE,
            [
                self::TYPE_RECTANGLE => $DIC->language()->txt('asq_option_rectangle'),
                self::TYPE_CIRCLE => $DIC->language()->txt('asq_option_circle'),
                self::TYPE_POLYGON => $DIC->language()->txt('asq_option_polygon')
            ]);

        $fields[] = new AsqTableInputFieldDefinition(
            $DIC->language()->txt('asq_label_coordinates'),
            AsqTableInputFieldDefinition::TYPE_LABEL,
            self::VAR_COORDINATES
            );

        $fields[] = new AsqTableInputFieldDefinition(
            '',
            AsqTableInputFieldDefinition::TYPE_HIDDEN,
            self::VAR_COORDINATES
            );

        $fields[] = new AsqTableInputFieldDefinition(
            '',
            AsqTableInputFieldDefinition::TYPE_BUTTON,
            'btn-coordinates',
            [
                'css' => 'js_select_coordinates',
                'title' => $DIC->language()->txt('asq_label_select_coordinates')
            ]);

        return $fields;
    }

    /**
     * @param string $index
     * @return ImageMapEditorDisplayDefinition
     */
    public static function getValueFromPost(string $index) {
        return ImageMapEditorDisplayDefinition::create(
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_TOOLTIP)]),
            InputHelper::readInt(self::getPostKey($index, self::VAR_TYPE)),
            AsqHtmlPurifier::getInstance()->purify($_POST[self::getPostKey($index, self::VAR_COORDINATES)]));
    }

    /**
     * {@inheritDoc}
     * @see \srag\asq\Domain\Model\Answer\Option\AnswerDefinition::getValues()
     */
    public function getValues() : array {
        return [
            self::VAR_TOOLTIP => $this->tooltip,
            self::VAR_TYPE => $this->type,
            self::VAR_COORDINATES => $this->coordinates
        ];
    }
}