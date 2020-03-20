<?php

namespace srag\asq\Infrastructure\Setup;

use Exception;
use ilContext;
use ilInitialisation;
use srag\asq\Infrastructure\Setup\lang\SetupLanguages;
use srag\asq\Infrastructure\Setup\sql\SetupDatabase;

require_once 'Services/Context/classes/class.ilContext.php';
require_once 'Services/Init/classes/class.ilInitialisation.php';

class Setup
{

    private function __construct()
    {

    }


    public static function new() : Setup
    {
        $obj = new self();

        return $obj;
    }


    public function run()
    {
        if (!file_exists(getcwd() . '/ilias.ini.php')) {
            header('Location: ./setup/setup.php');
            exit();
        }

        ilContext::init(ilContext::CONTEXT_WEB);
        ilInitialisation::initILIAS();
        try {
            SetupDatabase::new()->run();
            SetupLanguages::new()->run();
        } catch (Exception $e) {
            echo "Setup Failed: " . $e->getMessage();
        }
    }
}


