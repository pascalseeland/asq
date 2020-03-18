<?php
declare(strict_types=1);

namespace srag\asq\Domain\Projection;

use srag\CQRS\Aggregate\AggregateRoot;
use srag\asq\Domain\Model\Question;
use srag\asq\Infrastructure\Persistence\Projection\PublishedQuestionRepository;

/**
 * Class ProjectQuestions
 *
 * @license Extended GPL, see docs/LICENSE
 * @copyright 1998-2020 ILIAS open source
 *
 * @package srag/asq
 * @author  Adrian Lüthi <al@studer-raimann.ch>
 */
class ProjectQuestions {

    /**
     * @param AggregateRoot $projectee
     *
     * @return mixed|void
     */
	public function project(AggregateRoot $projectee) {
	    /** @var Question $projectee */
		$repository = new PublishedQuestionRepository();
        $repository->saveNewQuestionRevision($projectee);
	}
}