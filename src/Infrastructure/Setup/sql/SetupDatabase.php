<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Setup\sql;

use srag\asq\Infrastructure\Persistence\SimpleStoredAnswer;
use srag\asq\Infrastructure\Persistence\EventStore\QuestionEventStoreAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionAr;
use srag\asq\Infrastructure\Persistence\Projection\QuestionListItemAr;
use srag\asq\Infrastructure\Persistence\QuestionType;
use srag\asq\AsqGateway;
use srag\asq\Questions\Kprim\KprimChoiceQuestionGUI;
use srag\asq\Questions\MultipleChoice\SingleChoiceQuestionGUI;
use srag\asq\Questions\MultipleChoice\MultipleChoiceQuestionGUI;
use srag\asq\Questions\ErrorText\ErrorTextQuestionGUI;
use srag\asq\Questions\ImageMap\ImageMapQuestionGUI;
use srag\asq\Questions\Cloze\ClozeQuestionGUI;
use srag\asq\Questions\Numeric\NumericQuestionGUI;
use srag\asq\Questions\Formula\FormulaQuestionGUI;
use srag\asq\Questions\TextSubset\TextSubsetQuestionGUI;
use srag\asq\Questions\Ordering\OrderingQuestionGUI;
use srag\asq\Questions\Matching\MatchingQuestionGUI;
use srag\asq\Questions\Essay\EssayQuestionGUI;
use srag\asq\Questions\FileUpload\FileUploadQuestionGUI;
use srag\asq\Questions\Ordering\OrderingTextQuestionGUI;

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
        QuestionType::updateDB();
        
        $this->addQuestionTypes();
    }
    
    private function addQuestionTypes() : void
    {
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_single_answer', 
            SingleChoiceQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_multiple_answer',
            MultipleChoiceQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_kprim_answer',
            KprimChoiceQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_error_text',
            ErrorTextQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_image_map',
            ImageMapQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_cloze',
            ClozeQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_numeric',
            NumericQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_formula',
            FormulaQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_text_subset',
            TextSubsetQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_ordering',
            OrderingQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_matching',
            MatchingQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_essay',
            EssayQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_file_upload',
            FileUploadQuestionGUI::class
        );
        
        AsqGateway::get()->question()->addQuestionType(
            'asq_question_ordering_text',
            OrderingTextQuestionGUI::class
        );
    }
}