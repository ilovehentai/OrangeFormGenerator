<?php

namespace FormGenerator\FormGenerator;

/**
 * Description of FormTranslatorController
 *
 * @author josesantos
 */
class FormTranslatorController {
    
    private $_mTranslations_path;
    private $_mLocale;
    
    public function __construct($locale = "", $translations_path = "") {
        $this->_mLocale = $locale;
        $this->_mTranslations_path = $translations_path;
        
    }
    
    public function getTranslator() {
        return FromTranslationFactory::getFormTranslationInstance($this->_mLocale, $this->_mTranslations_path);
    }
    
}