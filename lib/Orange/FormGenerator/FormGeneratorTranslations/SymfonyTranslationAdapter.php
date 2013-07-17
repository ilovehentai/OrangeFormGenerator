<?php

namespace FormGenerator\FormGeneratorTranslations;
use Symfony\Component\Translation\Translator;

/**
 * Description of FormTranslation
 *
 * @author josesantos
 */
class SymfonyTranslationAdapter implements IFormTranslation{
    
    private $_local;
    private $_translator;
    private static $instance;
    
    public function __construct($locale = "") {
        $this->_translator = new Translator($locale);
    }

    public function getTranslation($text, array $parameters = array()) {
        return $this->_translator->trans($text, $parameters);
    }

    public function setLocale($locale) {
        $this->_local = $locale;
        $this->_translator->setLocale($locale);
    }
    
    public static function getInstance($locale = "") {
        return (is_null(self::$instance)) ? new SymfonyTranslationAdapter($locale) : self::$instance;
    }
}
