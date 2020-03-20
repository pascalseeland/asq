<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Setup\sql;

use srag\Plugins\AssessmentTest\Persistence\AssessmentResultEventStoreAr;
use srag\asq\Infrastructure\Persistence\SimpleStoredAnswer;
use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStoreAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionListItemAr;

/**
 * Class SetupDatabase
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class SetupDatabase {
	public function __contstruct() {

	}

	public function run(bool $clear = false):void {
	    global $DIC;

	    if ($clear) {
            $DIC->database()->dropTable(QuestionEventStoreAr::STORAGE_NAME, false);
            $DIC->database()->dropTable(QuestionListItemAr::STORAGE_NAME, false);
            $DIC->database()->dropTable(QuestionAr::STORAGE_NAME, false);
            $DIC->database()->dropTable(SimpleStoredAnswer::STORAGE_NAME, false);
            $DIC->database()->dropTable(AssessmentResultEventStoreAr::STORAGE_NAME, false);
	    }

        QuestionEventStoreAr::updateDB();
	    QuestionListItemAr::updateDB();
	    QuestionAr::updateDB();
	    SimpleStoredAnswer::updateDB();
        AssessmentResultEventStoreAr::updateDB();
	    
	    //Migration
        //Migrate Contentpage Definition (here for the implementation the migration)
        $DIC->database()->query("UPDATE copg_pobj_def SET parent_type = 'asqq' where component = 'Modules/TestQuestionPool' AND class_name = 'ilAssQuestionPage'");
        $DIC->database()->query("UPDATE copg_pobj_def SET component = 'Services/AssessmentQuestion',  class_name = 'AsqPageObject', directory = 'src/UserInterface/Web/Page' where parent_type = 'asqq'");

        $this->cleanupContentPages();

        echo "Setup wurde durchgefüht. Datentabellen wurden installiert / aktualisiert. ACHTUNG allenfalls muss vorher via setup/setup.php die Ctrl-Struktur neu geladen werden. In diesem Fall dieses Setup erneut ausführen.<br><br>";
        echo "Es müsste nun neben dem Setup / Resetup ASQ ein neuer Tab 'exAsqExamplesGUI' angezeigt werden<br><br>";

        echo "<a href='../../../../../'>zurück zu ILIAS</a>";

	}

	protected function cleanupContentPages()
    {
        global $DIC; /* @var \ILIAS\DI\Container $DIC */

        // question pages
        // old
        $DIC->database()->manipulateF(
            "DELETE FROM page_object WHERE parent_type = %s",
            ['text'], ['qpl']
        );
        // new
        $DIC->database()->manipulateF(
            "DELETE FROM page_object WHERE parent_type = %s",
            ['text'], ['asq']
        );


        // generic (correct/wrong) feedback pages
        // old
        $DIC->database()->manipulateF(
            "DELETE FROM page_object WHERE parent_type = %s",
            ['text'], ['afbg']
        );
        //new
        $DIC->database()->manipulateF(
            "DELETE FROM page_object WHERE parent_type = %s",
            ['text'], ['asqq']
        );

        // answer specific feedbacks
        // old
        $DIC->database()->manipulateF(
            "DELETE FROM page_object WHERE parent_type = %s",
            ['text'], ['qfbs']
        );
        // new
        $DIC->database()->manipulateF(
            "DELETE FROM page_object WHERE parent_type = %s",
            ['text'], ['asqa']
        );
    }
}