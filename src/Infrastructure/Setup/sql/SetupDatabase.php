<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Setup\sql;

use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStoreAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionListItemAr;
use srag\asq\Infrastructure\Persistence\SimpleStoredAnswer;
use srag\asq\UserInterface\Web\PathHelper;

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
        global $DIC;

        $DIC->database()->modifyTableColumn('copg_pobj_def','component',['length' => '521']);
        $DIC->database()->insert("copg_pobj_def", array(
            'parent_type' => array('text', 'asqq'), 'class_name' => array('text', 'AsqPageObject'), 'directory' => array('text', 'src/UserInterface/Web/Page'), 'component' => array('text', PathHelper::getBasePath(__DIR__)  )));

        QuestionEventStoreAr::updateDB();
        QuestionListItemAr::updateDB();
        QuestionAr::updateDB();
        SimpleStoredAnswer::updateDB();



    }
}