<?php
declare(strict_types=1);

namespace srag\asq\UserInterface\Web\Component\Presenter;



use srag\asq\Domain\QuestionDto;
use srag\asq\UserInterface\Web\Component\Editor\AbstractEditor;

/**
 * Abstract Class AbstractPresenter
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
abstract class AbstractPresenter {
	/**
	 * @var QuestionDto
	 */
	protected $question;
	/**
	 * @var AbstractEditor
	 */
	//protected $editor;

    /**
     * AbstractPresenter constructor.
     *
     * @param QuestionDto $question
     */
	public function __construct(QuestionDto $question) {
		$this->question = $question;
	}

    /**
     * @return AbstractEditor
     */
	/*public function getEditor() {
		return $this->editor;
	}*/

	/**
	 * @return string
	 */
	abstract public function generateHtml(AbstractEditor $editor): string;

	/**
	 * @return array|null
	 */
	public static function generateFields(): ?array {
		return null;
	}
}