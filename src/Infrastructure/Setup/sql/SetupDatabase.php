<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Setup\sql;

use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStoreAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionListItemAr;
use srag\asq\Infrastructure\Persistence\SimpleStoredAnswer;

/**
 * Class SetupDatabase
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class SetupDatabase
{

    private function __construct()
    {

    }


    public static function new() : SetupDatabase
    {
        return new self();
    }


    public function run() : void
    {
        QuestionEventStoreAr::updateDB();
        QuestionListItemAr::updateDB();
        QuestionAr::updateDB();
        SimpleStoredAnswer::updateDB();
    }
}