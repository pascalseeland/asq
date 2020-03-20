<?php
declare(strict_types=1);

namespace srag\asq\Infrastructure\Setup\lang;

use ilGlobalCache;
use ilObjLanguage;
use srag\asq\UserInterface\Web\PathHelper;

/**
 * Class SetupDatabase
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class SetupLanguages
{

    const LANG_PREFIX = "asq";
    /**
     * @var string
     */
    protected $lng_prefix;


    private function __construct()
    {
        $this->lng_prefix = self::LANG_PREFIX;
    }


    public static function new() : SetupLanguages
    {
        $obj = new SetupLanguages();

        return $obj;
    }


    public function run()
    {
        ilGlobalCache::flushAll();

        // get the keys of all installed languages if keys are not provided
        $a_lang_keys = array();
        foreach (ilObjLanguage::getInstalledLanguages() as $langObj) {
            if ($langObj->isInstalled()) {
                $a_lang_keys[] = $langObj->getKey();
            }
        }

        $langs = $this->getAvailableLangFiles($this->getLanguageDirectory());

        foreach ($langs as $lang) {
            // check if the language should be updated, otherwise skip it
            if (!in_array($lang['key'], $a_lang_keys)) {
                continue;
            }

            $txt = file($this->getLanguageDirectory() . "/" . $lang["file"]);
            $lang_array = array();

            // get locally changed variables of the module (these should be kept)
            $local_changes = ilObjLanguage::_getLocalChangesByModule($lang['key'], $this->lng_prefix);

            // get language data
            if (is_array($txt)) {
                foreach ($txt as $row) {
                    if ($row[0] != "#" && strpos($row, "#:#") > 0) {
                        $a = explode("#:#", trim($row));
                        $identifier = $this->lng_prefix . "_" . trim($a[0]);
                        $value = trim($a[1]);

                        if (isset($local_changes[$identifier])) {
                            $lang_array[$identifier] = $local_changes[$identifier];
                        } else {
                            $lang_array[$identifier] = $value;
                            ilObjLanguage::replaceLangEntry($this->lng_prefix, $identifier, $lang["key"], $value);
                        }
                    }
                }
            }
            ilObjLanguage::replaceLangModule($lang["key"], $this->lng_prefix, $lang_array);
        }
    }


    /**
     * Get array of all language files
     */
    public function getAvailableLangFiles(string $a_lang_directory) : array
    {
        $langs = array();

        if (!@is_dir($a_lang_directory)) {
            return array();
        }

        $dir = opendir($a_lang_directory);
        while ($file = readdir($dir)) {
            if ($file != "." and
                $file != ".."
            ) {
                // directories
                if (@is_file($a_lang_directory . "/" . $file)) {
                    if (substr($file, 0, 6) == "ilias_"
                        && substr($file, strlen($file) - 5) == ".lang"
                    ) {
                        $langs[] = array(
                            "key"  => substr($file, 6, 2),
                            "file" => $file,
                            "path" => $a_lang_directory . "/" . $file,
                        );
                    }
                }
            }
        }

        return $langs;
    }


    public function getLanguageDirectory() : string
    {
        return PathHelper::getBasePath(__DIR__) . 'lang';
    }
}

