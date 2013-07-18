<?php

namespace FormGenerator\FormGeneratorTranslations;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Description of FormTranslation
 *
 * @author josesantos
 */
class SymfonyTranslationAdapter implements IFormTranslation{
    
    private $_locale;
    private $_translator;
    private static $instance;
    
    public function __construct($locale = "", $translations_path = "") {
        $this->_translator = new Translator($locale);
        $this->setTranslationsPath($translations_path);
    }

    public function getTranslation($text, array $parameters = array()) {
        return $this->_translator->trans($text, $parameters);
    }

    public function setLocale($locale) {
        $this->_locale = $locale;
        $this->_translator->setLocale($locale);
    }

    public function setTranslationsPath($translations_path) {
        if (is_dir($translations_path)) {
            $this->_translator->addLoader('yaml', new YamlFileLoader());
            foreach (new \DirectoryIterator($translations_path) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                list($domain, $locale, $file) = explode(".", $fileInfo->getFilename());
                $this->_translator->addResource("yaml", $translations_path . $fileInfo->getFilename(), $locale);
            }
        }
    }
    
    public static function getInstance($locale = "", $translations_path = "") {
        return (is_null(self::$instance)) ? self::$instance = new SymfonyTranslationAdapter($locale, $translations_path) : self::$instance;
    }
}
